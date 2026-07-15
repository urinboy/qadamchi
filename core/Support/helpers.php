<?php
/**
 * Qadamchi global yordamchi funksiyalari (Laravel uslubida).
 * bootstrap/app.php da require_once orqali yuklanadi.
 * Sinflar qisqa aliaslar orqali (Route, View, Auth...) topiladi — facade g'oyasi.
 */

if (!function_exists('app')) {
    /**
     * Container'ni yoki undan yechilgan binding'ni qaytaradi.
     * app() -> Container; app('request') -> Request instance.
     */
    function app($abstract = null, array $params = [])
    {
        $container = \Qadamchi\Container\Container::getInstance();
        if ($abstract === null) {
            return $container;
        }
        return $container->make($abstract, $params);
    }
}

if (!function_exists('config')) {
    function config($key = null, $default = null)
    {
        /** @var \Qadamchi\Support\Config $config */
        $config = app(\Qadamchi\Support\Config::class);
        if ($key === null) {
            return $config;
        }
        return $config->get($key, $default);
    }
}

if (!function_exists('base_path')) {
    function base_path(string $path = ''): string
    {
        return rtrim(__DIR__ . '/../..', '/') . ($path !== '' ? '/' . ltrim($path, '/') : '');
    }
}

if (!function_exists('app_path')) {
    function app_path(string $path = ''): string
    {
        return base_path('app/' . ltrim($path, '/'));
    }
}

if (!function_exists('storage_path')) {
    function storage_path(string $path = ''): string
    {
        return base_path('storage/' . ltrim($path, '/'));
    }
}

if (!function_exists('public_path')) {
    function public_path(string $path = ''): string
    {
        return base_path('public/' . ltrim($path, '/'));
    }
}

if (!function_exists('database_path')) {
    /**
     * database/ papkasi yo'li. SQLite default fayli shu yerda:
     * database/database.sqlite. public/ tashqarisida — veb orqali yetib bormaydi.
     * Shuningdek: database/migrations, database/seeders, database/factories.
     */
    function database_path(string $path = ''): string
    {
        return base_path('database/' . ltrim($path, '/'));
    }
}

if (!function_exists('resource_path')) {
    /**
     * resources/ papkasi yo'li (Laravel uslubi).
     * Blade view'lar: resources/views.
     */
    function resource_path(string $path = ''): string
    {
        return base_path('resources/' . ltrim($path, '/'));
    }
}

if (!function_exists('lang_path')) {
    /**
     * lang/ papkasi yo'li (Laravel 11+ root lang/).
     * Tarjima fayllari: lang/{locale}/{file}.php.
     */
    function lang_path(string $path = ''): string
    {
        return base_path('lang/' . ltrim($path, '/'));
    }
}

if (!function_exists('route')) {
    function route(string $name, array $params = []): ?string
    {
        return \Qadamchi\Routing\Route::url($name, $params);
    }
}

if (!function_exists('view')) {
    function view(string $name, array $data = []): string
    {
        return \Qadamchi\View\View::render($name, $data);
    }
}

if (!function_exists('redirect')) {
    function redirect(string $url = null, int $status = 302): \Qadamchi\Http\Response
    {
        return \Qadamchi\Http\Response::redirect($url, $status);
    }
}

if (!function_exists('back')) {
    function back(int $status = 302): \Qadamchi\Http\Response
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        return \Qadamchi\Http\Response::redirect($referer, $status);
    }
}

if (!function_exists('request')) {
    /**
     * request() -> Request instance; request('key') -> input qiymati.
     */
    function request($key = null, $default = null)
    {
        $req = \Qadamchi\Http\Request::instance();
        if ($key === null) {
            return $req;
        }
        return $req->input($key, $default);
    }
}

if (!function_exists('session')) {
    function session($key = null, $default = null)
    {
        $session = \Qadamchi\Http\Session::instance();
        if ($key === null) {
            return $session;
        }
        return $session->get($key, $default);
    }
}

if (!function_exists('old')) {
    function old(string $key, $default = null)
    {
        $old = \Qadamchi\Http\Session::instance()->get('_old_input', []);
        return $old[$key] ?? $default ?? '';
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        return \Qadamchi\Http\CSRF::token();
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return \Qadamchi\Http\CSRF::field();
    }
}

if (!function_exists('auth')) {
    function auth()
    {
        return new class {
            public function check() { return \Qadamchi\Auth\Auth::check(); }
            public function user()  { return \Qadamchi\Auth\Auth::user(); }
            public function id()    { return \Qadamchi\Auth\Auth::id(); }
            public function guest() { return !\Qadamchi\Auth\Auth::check(); }
            public function attempt(array $credentials) { return \Qadamchi\Auth\Auth::attempt($credentials); }
            public function login($user) { \Qadamchi\Auth\Auth::login($user); }
            public function logout() { \Qadamchi\Auth\Auth::logout(); }
        };
    }
}

if (!function_exists('bcrypt')) {
    function bcrypt(string $value): string
    {
        return \Qadamchi\Security\Security::hashPassword($value);
    }
}

if (!function_exists('asset')) {
    function asset(string $path = ''): string
    {
        return rtrim(config('app.url', ''), '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        if (preg_match('#^https?://#', $path)) {
            return $path;
        }
        return rtrim(config('app.url', ''), '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('e')) {
    function e($value): string
    {
        return \Qadamchi\Security\Security::escape($value === null ? '' : (string) $value);
    }
}

if (!function_exists('clean')) {
    function clean($value)
    {
        return \Qadamchi\Security\Security::sanitize($value);
    }
}

if (!function_exists('component')) {
    function component(string $name, array $data = [], $slot = null): string
    {
        return \Qadamchi\View\View::component($name, $data, $slot);
    }
}

if (!function_exists('trans')) {
    function trans(string $key, array $replace = []): string
    {
        return \Qadamchi\Support\Lang::get($key, $replace);
    }
}

if (!function_exists('trans_choice')) {
    function trans_choice(string $key, int $number, array $replace = []): string
    {
        return \Qadamchi\Support\Lang::choice($key, $number, $replace);
    }
}

if (!function_exists('dd')) {
    function dd(...$vars): void
    {
        foreach ($vars as $v) {
            echo '<pre style="background:#f4f4f4;padding:12px;border:1px solid #ddd;border-radius:6px;font-size:13px;overflow:auto">';
            var_dump($v);
            echo '</pre>';
        }
        die(1);
    }
}

if (!function_exists('dump')) {
    function dump(...$vars): void
    {
        foreach ($vars as $v) {
            echo '<pre style="background:#f4f4f4;padding:12px;border:1px solid #ddd;border-radius:6px;font-size:13px;overflow:auto">';
            var_dump($v);
            echo '</pre>';
        }
    }
}