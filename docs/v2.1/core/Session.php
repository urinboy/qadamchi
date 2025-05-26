<?php
class Session {
    public static function put($key, $value) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION[$key] = $value;
    }
    public static function get($key, $default = null) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        return $_SESSION[$key] ?? $default;
    }
}