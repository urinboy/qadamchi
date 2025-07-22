<?php

// core/CSRF.php - yangi fayl
class CSRF {
    const TOKEN_NAME = '_token';
    
    public static function generateToken() {
        if (!isset($_SESSION[self::TOKEN_NAME])) {
            $_SESSION[self::TOKEN_NAME] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::TOKEN_NAME];
    }
    
    public static function getToken() {
        return $_SESSION[self::TOKEN_NAME] ?? null;
    }
    
    public static function validateToken($token) {
        $sessionToken = static::getToken();
        return $sessionToken && hash_equals($sessionToken, $token);
    }
    
    public static function field() {
        $token = static::generateToken();
        return "<input type=\"hidden\" name=\"" . self::TOKEN_NAME . "\" value=\"$token\">";
    }
}
