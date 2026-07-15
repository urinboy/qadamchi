<?php
namespace Qadamchi\Auth;

use Qadamchi\Http\Session;
use Qadamchi\Security\Security;
use Qadamchi\Database\Model;

/**
 * Auth — session-based autentifikatsiya (Laravel'ning Auth g'oyasi).
 *
 * Statik facade API (Laravel'ga o'xshash):
 *   Auth::attempt($credentials) — model'dan qidir + parol tekshir + login (session regenerate)
 *   Auth::login($user) / Auth::logout() / Auth::user() / Auth::id() / Auth::check() / Auth::guest()
 *
 * Ichki holat (config, session) instance orqali boshqariladi.
 */
class Auth
{
    protected static ?Auth $instance = null;
    protected array $config;

    public static function instance(): self
    {
        return self::$instance ??= new self();
    }

    public function __construct()
    {
        $this->config = config('auth', [
            'provider' => ['model' => 'App\\Models\\User'],
            'session_key' => 'user_id',
        ]);
    }

    /** Credentiallar bo'yicha urinish (email/username + password). */
    public static function attempt(array $credentials): bool
    {
        $inst = self::instance();
        $model = $inst->userModel();
        $passwordField = $inst->config['password_field'] ?? 'password';
        $password = $credentials[$passwordField] ?? null;
        unset($credentials[$passwordField]);

        $query = $model::query();
        foreach ($credentials as $k => $v) {
            $query = $query->where($k, $v);
        }
        $user = $query->first();

        if (!$user || $password === null) {
            return false;
        }

        $hash = $user->getAttribute($passwordField) ?? ($user->getAttribute('password'));
        if (!Security::verifyPassword((string) $password, (string) $hash)) {
            return false;
        }

        self::login($user);
        return true;
    }

    public static function login($user): void
    {
        if ($user instanceof Model) {
            Session::instance()->put(self::instance()->sessionKey(), $user->getKey());
            Session::instance()->put('_user_class', get_class($user));
        } else {
            Session::instance()->put('user_raw', $user);
        }
        Session::instance()->regenerate(true);
    }

    public static function logout(): void
    {
        Session::instance()->forget(self::instance()->sessionKey());
        Session::instance()->forget('_user_class');
        Session::instance()->forget('user_raw');
        Session::instance()->regenerate(true);
    }

    public static function user()
    {
        $raw = Session::instance()->get('user_raw');
        if ($raw !== null) {
            return $raw;
        }
        $id = Session::instance()->get(self::instance()->sessionKey());
        if (!$id) return null;
        $class = Session::instance()->get('_user_class', self::instance()->userModel());
        if (!class_exists($class)) return null;
        return $class::find($id);
    }

    public static function id()
    {
        $user = self::user();
        if (!$user) return null;
        if ($user instanceof Model) return $user->getKey();
        return $user['id'] ?? null;
    }

    public static function check(): bool { return self::user() !== null; }
    public static function guest(): bool { return !self::check(); }

    public static function guard(string $name = 'web'): self { return self::instance(); }

    protected function userModel(): string
    {
        return $this->config['provider']['model'] ?? ($this->config['model'] ?? 'App\\Models\\User');
    }

    protected function sessionKey(string $alt = null): string
    {
        if ($alt) return $alt;
        return $this->config['session_key'] ?? 'user_id';
    }
}