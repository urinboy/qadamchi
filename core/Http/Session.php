<?php
namespace Qadamchi\Http;

/**
 * Session manager (Laravel'ning Session g'oyasi, kam kod).
 * Flash data: keyingi so'rovda yashaydi, keyin o'chadi.
 */
class Session
{
    protected static ?Session $instance = null;

    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $this->start();
    }

    public function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }
        if (headers_sent()) {
            // Header'lar allaqachon yuborilgan (masalan, test runner'da) —
            // haqiqiy session boshlab bo'lmaydi, $_SESSION ni oddiy massiv sifatida ishlatamiz.
            if (!isset($_SESSION)) {
                $_SESSION = [];
            }
            return;
        }
        $cookie = config('session', []);
        if (is_array($cookie) && !empty($cookie)) {
            session_set_cookie_params([
                'lifetime' => $cookie['lifetime'] ?? 0,
                'path'     => $cookie['path'] ?? '/',
                'httponly' => $cookie['httponly'] ?? true,
                'samesite' => $cookie['samesite'] ?? 'Lax',
                'secure'   => $cookie['secure'] ?? false,
            ]);
        }
        session_start();
    }

    public function put(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $_SESSION);
    }

    public function all(): array
    {
        return $_SESSION;
    }

    public function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function flush(): void
    {
        $_SESSION = [];
    }

    /** Flash: keyingi so'rovda yashaydi. */
    public function flash(string $key, $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    public function reflash(): void
    {
        foreach (($_SESSION['_flash'] ?? []) as $k => $v) {
            $_SESSION['_flash'][$k] = $v;
        }
    }

    public function keep(array $keys): void
    {
        foreach ($keys as $k) {
            if (isset($_SESSION['_flash'][$k])) {
                // qiymatni saqlab qolamiz (keyingi so'rovda ham yashasin)
                $_SESSION['_flash'][$k] = $_SESSION['_flash'][$k];
            }
        }
    }

    public function getFlash(string $key, $default = null)
    {
        return $_SESSION['_flash'][$key] ?? $default;
    }

    public function token(): string
    {
        return $this->get('_token', '');
    }

    public function regenerate(bool $destroy = false): void
    {
        if (session_status() === PHP_SESSION_ACTIVE && !headers_sent()) {
            session_regenerate_id($destroy);
        }
    }

    public function clearFlash(): void
    {
        unset($_SESSION['_flash']);
    }

    /** Eski statik API bilan moslik. */
    public static function __callStatic($name, $arguments)
    {
        return self::instance()->{$name}(...$arguments);
    }
}