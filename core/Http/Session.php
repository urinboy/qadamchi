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
            $this->ageFlash();
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
        $this->ageFlash();
    }

    /**
     * Flash'ni "eslatadi" (Laravel ageFlash) — har so'rov boshida bir marta.
     * Oldingi so'rovda yozilgan _flash_new joriy o'qish qatlamiga (_flash) ko'chiriladi,
     * u erda turgan eskilar esa tashlab yuboriladi. Shunda flash aynan bitta
     * keyingi so'rovda yashaydi va o'z-o'zidan yo'qolib ketadi — doimiy alert emas.
     */
    public function ageFlash(): void
    {
        $_SESSION['_flash'] = $_SESSION['_flash_new'] ?? [];
        unset($_SESSION['_flash_new']);
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

    /** Flash: keyingi so'rovda yashaydi — yangi qatlamga (_flash_new) yoziladi. */
    public function flash(string $key, $value): void
    {
        $_SESSION['_flash_new'][$key] = $value;
    }

    /** Joriy so'rovdagi flash'ni yana bir so'rovga uzaytiradi. */
    public function reflash(): void
    {
        foreach (($_SESSION['_flash'] ?? []) as $k => $v) {
            $_SESSION['_flash_new'][$k] = $v;
        }
    }

    /** Faqat ko'rsatilgan kalitlarni yana bir so'rovga uzaytiradi. */
    public function keep(array $keys): void
    {
        foreach ($keys as $k) {
            if (array_key_exists($k, $_SESSION['_flash'] ?? [])) {
                $_SESSION['_flash_new'][$k] = $_SESSION['_flash'][$k];
            }
        }
    }

    /** Joriy (aged) flash'dan o'qiydi — bu so'rovda, _flash_new emas. */
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
        unset($_SESSION['_flash'], $_SESSION['_flash_new']);
    }

    /** Eski statik API bilan moslik. */
    public static function __callStatic($name, $arguments)
    {
        return self::instance()->{$name}(...$arguments);
    }
}