<?php
// Static fayllarni to'g'ri uzatish uchun
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$publicPath = __DIR__ . $uri;
if (php_sapi_name() === 'cli-server' && is_file($publicPath)) {
    return false; // Static faylni PHP server o'zi beradi
}

// Simple PSR-4 like autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // Core klasslari uchun
        $coreFile = __DIR__ . '/../core/' . str_replace('\\', '/', $class) . '.php';
        if (file_exists($coreFile)) {
            require $coreFile;
        }
        return;
    }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

require_once __DIR__ . '/../core/dotenv.php';
load_env();

// Yordamchi funksiyalar (masalan, route())
$helpers = __DIR__ . '/../core/helpers.php';
if (file_exists($helpers)) require_once $helpers;

// Load routes
require __DIR__ . '/../core/Route.php';
require __DIR__ . '/../routes/web.php';

// Dispatch routes
Route::dispatch();