<?php
namespace Qadamchi\Security;

/**
 * XSS va parol xavfsizligi yordamchilari.
 */
class Security
{
    public static function escape($data)
    {
        if (is_array($data)) {
            return array_map([self::class, 'escape'], $data);
        }
        if ($data === null || is_bool($data)) {
            return $data;
        }
        return htmlspecialchars((string) $data, ENT_QUOTES, 'UTF-8');
    }

    public static function sanitize($data)
    {
        if (is_array($data)) {
            return array_map([self::class, 'sanitize'], $data);
        }
        if ($data === null) {
            return null;
        }
        return trim(strip_tags((string) $data));
    }

    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}