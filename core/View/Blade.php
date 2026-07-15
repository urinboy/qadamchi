<?php
namespace Qadamchi\View;

/**
 * Blade'ga o'xshash shablon dvigateli (kam kod bilan).
 * {{ }} (escape), {!! !!}, @if/@foreach/@section/@yield/@extends/@include/@csrf/@method va h.k.
 * Layout inheritance: @extends + @section/@yield.
 */
class Blade
{
    protected static array $sections = [];
    protected static array $sectionStack = [];
    protected static ?string $layout = null;

    public static function reset(): void
    {
        self::$sections = [];
        self::$sectionStack = [];
        self::$layout = null;
    }

    public static function extend(string $layout): void
    {
        self::$layout = $layout;
    }

    public static function getLayout(): ?string
    {
        return self::$layout;
    }

    public static function startSection(string $name): void
    {
        self::$sectionStack[] = $name;
        ob_start();
    }

    public static function stopSection(): void
    {
        $name = array_pop(self::$sectionStack);
        if ($name === null) return;
        self::$sections[$name] = ob_get_clean();
    }

    public static function singleSection(string $name, string $value): void
    {
        self::$sections[$name] = $value;
    }

    public static function yieldSection(string $name): string
    {
        return self::$sections[$name] ?? '';
    }

    /**
     * Balanslangan qavsli ifodani oladi: "(isset($x))" -> "isset($x)".
     * PCRE rekursiyasi (?1) ichki qavslarni hisoblaydi.
     */
    protected static function balancedExpr(string $directive): string
    {
        return '/@' . $directive . '\s*(\((?:[^()]+|(?1))*\))/';
    }

    protected static function inner(string $parens): string
    {
        return substr($parens, 1, -1);
    }

    /** Compile: Blade sintaksisi -> PHP. */
    public static function compileString(string $template): string
    {
        // 1) Izohlar
        $template = preg_replace('/\{\{--(.*?)--\}\}/s', '<?php /* $1 */ ?>', $template);

        // 2) @php ... @endphp
        $template = preg_replace('/@php\s*(.*?)\s*@endphp/s', '<?php $1 ?>', $template);

        // 3) @csrf / @method
        $template = preg_replace('/@csrf/', '<?php echo csrf_field(); ?>', $template);
        $template = preg_replace('/@method\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)/', '<?php echo \'<input type="hidden" name="_method" value="$1">\'; ?>', $template);

        // 4) @extends
        $template = preg_replace('/@extends\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)/', '<?php \Qadamchi\View\Blade::extend(\'$1\'); ?>', $template);

        // 5) @yield
        $template = preg_replace('/@yield\s*\(\s*[\'"]([^\'"]+)[\'"]\s*(?:,\s*[\'"]([^\'"]*)[\'"]\s*)?\)/', '<?php echo \Qadamchi\View\Blade::yieldSection(\'$1\') ?: \'$2\'; ?>', $template);

        // 6) @include('x', [...])
        $template = preg_replace_callback('/@include\s*\(\s*[\'"]([^\'"]+)[\'"]\s*(?:,\s*(\[.*?\]))?\s*\)/', function ($m) {
            $name = $m[1];
            $extra = $m[2] ?? '[]';
            return "<?php echo \Qadamchi\View\View::render('$name', array_merge(get_defined_vars(), $extra)); ?>";
        }, $template);

        // 7) @section('x') ... @endsection
        $template = preg_replace_callback('/@section\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)(.*?)@endsection/s', function ($m) {
            $name = $m[1];
            $body = $m[2];
            return "<?php \Qadamchi\View\Blade::startSection('$name'); ?>" . $body . "<?php \Qadamchi\View\Blade::stopSection(); ?>";
        }, $template);

        // 8) @section('x','default') — oddiy qiymat
        $template = preg_replace('/@section\s*\(\s*[\'"]([^\'"]+)[\'"]\s*,\s*[\'"]([^\'"]*)[\'"]\s*\)/', '<?php \Qadamchi\View\Blade::singleSection(\'$1\', \'$2\'); ?>', $template);

        // 9) @if / @elseif / @else / @endif — balanslangan qavslar (ichki funksiyalar bilan)
        $template = preg_replace_callback(self::balancedExpr('if'), fn($m) => '<?php if(' . self::inner($m[1]) . '): ?>', $template);
        $template = preg_replace_callback(self::balancedExpr('elseif'), fn($m) => '<?php elseif(' . self::inner($m[1]) . '): ?>', $template);
        $template = preg_replace('/@else\b/', '<?php else: ?>', $template);
        $template = preg_replace('/@endif\b/', '<?php endif; ?>', $template);

        // 10) @foreach / @endforeach
        $template = preg_replace_callback(self::balancedExpr('foreach'), fn($m) => '<?php foreach(' . self::inner($m[1]) . '): ?>', $template);
        $template = preg_replace('/@endforeach\b/', '<?php endforeach; ?>', $template);

        // 11) @forelse / @empty / @endforelse
        $template = preg_replace_callback(self::balancedExpr('forelse'), function ($m) {
            $expr = self::inner($m[1]);
            $key = md5($expr);
            return '<?php $__forelse_' . $key . ' = ' . $expr . '; $__forelse_i = 0; foreach($__forelse_' . $key . ' as $__item): $__forelse_i++; ?>';
        }, $template);
        $template = preg_replace('/@empty\b/', '<?php endforeach; if($__forelse_i === 0): ?>', $template);
        $template = preg_replace('/@endforelse\b/', '<?php endif; ?>', $template);

        // 12) @isset / @endisset
        $template = preg_replace_callback(self::balancedExpr('isset'), fn($m) => '<?php if(isset(' . self::inner($m[1]) . ')): ?>', $template);
        $template = preg_replace('/@endisset\b/', '<?php endif; ?>', $template);

        // 13) @auth / @guest
        $template = preg_replace('/@auth\b/', '<?php if(auth()->check()): ?>', $template);
        $template = preg_replace('/@endauth\b/', '<?php endif; ?>', $template);
        $template = preg_replace('/@guest\b/', '<?php if(auth()->guest()): ?>', $template);
        $template = preg_replace('/@endguest\b/', '<?php endif; ?>', $template);

        // 14) {!! !!} — ekranga xom chiqarish
        $template = preg_replace_callback('/\{!!\s*(.+?)\s*!!\}/', function ($m) {
            return '<?php echo ' . $m[1] . '; ?>';
        }, $template);

        // 15) {{ }} — escape bilan
        $template = preg_replace_callback('/\{\{\s*(.+?)\s*\}\}/', function ($m) {
            return '<?php echo e(' . $m[1] . '); ?>';
        }, $template);

        return $template;
    }
}