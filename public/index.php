<?php
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