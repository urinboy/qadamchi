<?php
if (!function_exists('route')) {
    function route($name, $params = [])
    {
        return Route::url($name, $params);
    }
}

function dd($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die();
}

function old($key, $default = null) {
    return Session::get('_old_input')[$key] ?? $default;
}

function back() {
    $referer = $_SERVER['HTTP_REFERER'] ?? '/';
    return header("Location: $referer");
}

function csrf_token() {
    return CSRF::generateToken();
}

function csrf_field() {
    return CSRF::field();
}

function auth() {
    return new class {
        public function check() {
            return Auth::check();
        }
        
        public function user() {
            return Auth::user();
        }
        
        public function id() {
            $user = Auth::user();
            return $user ? $user->id : null;
        }
    };
}