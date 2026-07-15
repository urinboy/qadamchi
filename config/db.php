<?php
return [
    'driver' => env('DB_CONNECTION', 'mysql'),
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', 3306),
    'name' => env('DB_DATABASE', 'qadamchi'),
    'user' => env('DB_USERNAME', 'root'),
    'pass' => env('DB_PASSWORD', ''),
    'charset' => env('DB_CHARSET', 'utf8mb4'),
];