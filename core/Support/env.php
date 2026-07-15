<?php
/**
 * Mini Dotenv loader (Composer'siz, frameworklarsiz).
 * load_env() bir martadan ortiqq ishlamasligi uchun statik flag bilan himoyalangan.
 *
 * Eslatma: bu fayl PSR-4 autoloaderga kirmaydi (procedural funksiyalar).
 * bootstrap/app.php da require_once orqali yuklanadi.
 */

if (!function_exists('load_env')) {
    function load_env($path = null) {
        static $loaded = false;
        if ($loaded) return;
        $loaded = true;

        $envPath = $path ?? __DIR__ . '/../../.env';
        if (!is_file($envPath)) return;

        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#') continue;
            if (strpos($line, '=') === false) continue;
            if (str_starts_with($line, 'export ')) {
                $line = trim(substr($line, 7));
            }

            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Qo'shtirnoq ichidagi qiymatdan qo'shtirnoqni olib tashlash
            if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                $value = substr($value, 1, -1);
            }

            // Faqat allaqachon o'rnatilmagan bo'lsa o'rnatamiz (real env'ga ustunlik)
            if (getenv($key) === false && !isset($_ENV[$key])) {
                putenv("$key=$value");
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }
    }
}

if (!function_exists('env')) {
    /**
     * Muhit o'zgaruvchisini o'qish (Laravel uslubida).
     * env('KEY', 'default') — true/false/null/numeric avtomatik aniqlaydi.
     */
    function env($key, $default = null) {
        $value = getenv($key);
        if ($value === false) {
            return $default;
        }
        $lower = strtolower($value);
        if ($lower === 'true') return true;
        if ($lower === 'false') return false;
        if ($lower === 'null' || $lower === '(null)') return null;
        if ($lower === 'empty' || $lower === '(empty)') return '';
        if (is_numeric($value)) return $value + 0;
        return $value;
    }
}