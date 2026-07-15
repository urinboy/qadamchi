<?php
namespace Qadamchi\Http;

use Qadamchi\Http\Session;

/**
 * CSRF himoyasi — session token, timing-safe solash (hash_equals).
 */
class CSRF
{
    public const TOKEN_NAME = '_token';

    public static function token(): string
    {
        $session = Session::instance();
        $token = $session->get(self::TOKEN_NAME);
        if (!$token) {
            $token = bin2hex(random_bytes(32));
            $session->put(self::TOKEN_NAME, $token);
        }
        return $token;
    }

    public static function validateToken(?string $token): bool
    {
        $sessionToken = Session::instance()->get(self::TOKEN_NAME);
        if (!$sessionToken || !$token) {
            return false;
        }
        return hash_equals($sessionToken, $token);
    }

    public static function field(): string
    {
        $token = self::token();
        return '<input type="hidden" name="' . self::TOKEN_NAME . '" value="' . $token . '">';
    }

    /** Eski nom bilan moslik. */
    public static function generateToken(): string
    {
        return self::token();
    }

    public static function getToken(): ?string
    {
        return Session::instance()->get(self::TOKEN_NAME);
    }
}