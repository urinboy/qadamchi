<?php
// Static fayllarni to'g'ri uzatish uchun
$publicPath = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (php_sapi_name() === 'cli-server' && is_file($publicPath)) {
    return false; // Static faylni PHP server o'zi beradi
}


// Simple autoloader
spl_autoload_register(function ($class) {
    $class = str_replace('\\', '/', $class);
    if (file_exists(__DIR__ . '/../core/' . basename($class) . '.php')) {
        require __DIR__ . '/../core/' . basename($class) . '.php';
    }
    if (file_exists(__DIR__ . '/../app/Controllers/' . basename($class) . '.php')) {
        require __DIR__ . '/../app/Controllers/' . basename($class) . '.php';
    }
});

// Load routes
require __DIR__ . '/../core/Route.php';
require __DIR__ . '/../routes/web.php';

// Dispatch routes
Route::dispatch();