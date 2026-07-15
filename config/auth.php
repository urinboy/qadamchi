<?php
return [
    // Web guard — session based
    'provider' => [
        'model' => env('AUTH_MODEL', 'App\\Models\\User'),
    ],
    'password_field' => 'password',
    'session_key' => 'user_id',
];