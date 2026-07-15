<?php
namespace Qadamchi\View;

use Qadamchi\Http\Session;
use Qadamchi\Http\Request;

/**
 * View — shablonni render qilish (Blade compile + cache).
 * View::render('layouts.app', ['title'=>'Salom']) -> string.
 * views: app/Views/{name}.php yoki .blade.php
 */
class View
{
    protected static string $viewPath = '';
    protected static string $cachePath = '';

    public static function setPaths(string $views, string $cache): void
    {
        self::$viewPath = $views;
        self::$cachePath = $cache;
    }

    public static function render(string $name, array $data = []): string
    {
        $path = self::resolve($name);
        if ($path === null) {
            throw new \RuntimeException("View topilmadi: $name");
        }

        $compiled = self::compile($path);

        // Layout bilan ishlash uchun Blade holatini tiklaymiz
        Blade::reset();

        $output = self::renderCompiled($compiled, $data);

        // Agar @extends bo'lsa — layout'ni render qilamiz (sections to'ldirilgan)
        $layout = Blade::getLayout();
        if ($layout) {
            $layoutCompiled = self::compile(self::resolve($layout));
            $output = self::renderCompiled($layoutCompiled, $data);
        }

        return $output;
    }

    public static function component(string $name, array $data = [], $slot = null): string
    {
        return self::render("components/$name", array_merge($data, ['slot' => $slot]));
    }

    public static function exists(string $name): bool
    {
        return self::resolve($name) !== null;
    }

    protected static function resolve(string $name): ?string
    {
        $base = self::$viewPath ?: base_path('app/Views');
        $relative = str_replace('.', '/', $name);

        foreach (["$relative.blade.php", "$relative.php"] as $file) {
            $full = $base . '/' . $file;
            if (is_file($full)) return $full;
        }
        return null;
    }

    protected static function compile(string $path): string
    {
        $cache = self::$cachePath ?: storage_path('framework/views');
        if (!is_dir($cache)) {
            @mkdir($cache, 0775, true);
        }
        $key = sha1($path . filemtime($path));
        $compiled = $cache . '/' . $key . '.php';

        if (!is_file($compiled) || filemtime($path) > filemtime($compiled)) {
            $source = file_get_contents($path);
            $php = Blade::compileString($source);
            file_put_contents($compiled, $php);
        }
        return $compiled;
    }

    protected static function renderCompiled(string $compiledFile, array $data): string
    {
        // Blade view'lar uchun har doim mavjud o'zgaruvchilar
        $errors = Session::instance()->getFlash('_errors', []);
        $request = Request::instance();

        extract($data, EXTR_SKIP);
        if (!isset($errors)) $errors = [];
        if (!isset($request)) $request = Request::instance();

        ob_start();
        include $compiledFile;
        return ob_get_clean();
    }
}