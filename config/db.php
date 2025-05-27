<?php
require_once __DIR__ . '/../core/dotenv.php';
load_env();

return [
    'driver' => env('DB_CONNECTION', 'mysql'),
    'host' => env('DB_HOST', '127.127.126.4'),
    'name' => env('DB_DATABASE', 'qadamchi'),
    'user' => env('DB_USERNAME', 'root'),
    'pass' => env('DB_PASSWORD', ''),
    'charset' => env('DB_CHARSET', 'utf8mb4'),
];