<?php
// namespace Core;

/**
 * Mini Dotenv Loader (frameworksiz)
 * Faqat bir marta chaqirilsa ham yetarli.
 */

function load_env($path = null) {
    static $loaded = false;
    if ($loaded) return;
    $loaded = true;

    $envPath = $path ?? __DIR__ . '/../.env';
    if (!file_exists($envPath)) return;

    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        if (!strpos($line, '=')) continue;
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        // Agar "" yoki '' ichida bo'lsa, olib tashlaymiz
        if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
            (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
            $value = substr($value, 1, -1);
        }
        // Faqat o'qilmagan bo'lsa, o'rnatamiz
        if (getenv($key) === false && !isset($_ENV[$key])) {
            putenv("$key=$value");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}

/**
 * Qulay helper (Laravel uslubida)
 * env('KEY', 'default')
 */
function env($key, $default = null) {
    $value = getenv($key);
    if ($value === false) {
        return $default;
    }
    // true/false/string/numberni avtomatik aniqlash
    $lower = strtolower($value);
    if ($lower === 'true') return true;
    if ($lower === 'false') return false;
    if (is_numeric($value)) return $value + 0;
    return $value;
}