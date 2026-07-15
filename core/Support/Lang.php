<?php
namespace Qadamchi\Support;

/**
 * Tarjima (Laravel'ning trans() g'oyasi).
 * app/Lang/{locale}/{file}.php massivlardan o'qiydi.
 * Lang::get('auth.failed'), trans('messages.welcome', ['name'=>'Ali'])
 */
class Lang
{
    protected static string $locale = 'uz';
    protected static string $fallback = 'uz';
    protected static array $cache = [];

    public static function setLocale(string $locale): void
    {
        self::$locale = $locale;
    }

    public static function locale(): string
    {
        return self::$locale;
    }

    public static function get(string $key, array $replace = []): string
    {
        [$file, $line] = self::split($key);
        $value = self::line($file, $line, self::$locale)
            ?? self::line($file, $line, self::$fallback)
            ?? $key;
        return self::interpolate($value, $replace);
    }

    public static function choice(string $key, int $number, array $replace = []): string
    {
        $value = self::get($key, $replace);
        // Oddiy plural: "none|one|many" yoki "|one|many" format
        $parts = explode('|', $value);
        if (count($parts) === 1) return self::interpolate($value, $replace + ['count' => $number]);
        if ($number === 0 && isset($parts[0]) && $parts[0] !== '') $choice = $parts[0];
        elseif ($number === 1) $choice = $parts[1] ?? $parts[0];
        else $choice = $parts[2] ?? $parts[1] ?? $parts[0];
        return self::interpolate($choice, $replace + ['count' => $number]);
    }

    protected static function split(string $key): array
    {
        $segments = explode('.', $key);
        $file = array_shift($segments);
        return [$file, implode('.', $segments)];
    }

    protected static function line(string $file, string $line, string $locale): ?string
    {
        $items = self::load($file, $locale);
        $keys = explode('.', $line);
        foreach ($keys as $k) {
            if (!is_array($items) || !array_key_exists($k, $items)) return null;
            $items = $items[$k];
        }
        return is_string($items) ? $items : null;
    }

    protected static function load(string $file, string $locale): array
    {
        if (isset(self::$cache[$locale][$file])) {
            return self::$cache[$locale][$file];
        }
        $path = base_path("app/Lang/$locale/$file.php");
        self::$cache[$locale][$file] = is_file($path) ? (array) require $path : [];
        return self::$cache[$locale][$file];
    }

    protected static function interpolate(string $message, array $replace): string
    {
        foreach ($replace as $k => $v) {
            $message = str_replace(':' . $k, (string) $v, $message);
        }
        return $message;
    }
}