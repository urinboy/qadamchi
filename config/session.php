<?php
return [
    'lifetime' => 7200, // soniyalarda
    'path' => '/',
    'httponly' => true,
    'samesite' => 'Lax',
    'secure' => env('APP_ENV') === 'production',
];