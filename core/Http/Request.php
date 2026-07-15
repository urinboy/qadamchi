<?php
namespace Qadamchi\Http;

use Qadamchi\Http\Session;
use Qadamchi\Validation\Validator;
use Qadamchi\Exceptions\ValidationException;

/**
 * HTTP so'rov (instance). Laravel'ning Request g'oyasi.
 * input/all/only/except/has/file/header/method + validate().
 */
class Request
{
    protected array $query;
    protected array $post;
    protected array $server;
    protected array $files;
    protected ?array $jsonCache = null;
    protected array $routeParams = [];

    protected static ?Request $instance = null;

    public function __construct(array $query = [], array $post = [], array $server = [], array $files = [])
    {
        $this->query = $query;
        $this->post = $post;
        $this->server = $server;
        $this->files = $files;
    }

    public static function capture(): self
    {
        $req = new self($_GET, $_POST, $_SERVER, $_FILES);
        self::$instance = $req;
        return $req;
    }

    public static function instance(): self
    {
        return self::$instance ?? self::capture();
    }

    public static function setInstance(self $request): void
    {
        self::$instance = $request;
    }

    /** HTTP metodi (POST + _method spoofing bilan). */
    public function method(): string
    {
        $method = strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
        if ($method === 'POST') {
            $override = strtoupper($this->post['_method'] ?? '');
            if (in_array($override, ['PUT', 'PATCH', 'DELETE'], true)) {
                return $override;
            }
        }
        return $method;
    }

    public function isMethod(string $method): bool
    {
        return strcasecmp($this->method(), $method) === 0;
    }

    /** GET + POST (+ JSON body) birlashtirilib. */
    public function input(string $key = null, $default = null)
    {
        $all = $this->all();
        if ($key === null) {
            return $all;
        }
        return $all[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($this->query, $this->post, $this->json());
    }

    public function only($keys): array
    {
        $keys = is_array($keys) ? $keys : func_get_args();
        return array_intersect_key($this->all(), array_flip($keys));
    }

    public function except($keys): array
    {
        $keys = is_array($keys) ? $keys : func_get_args();
        return array_diff_key($this->all(), array_flip($keys));
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->all());
    }

    public function query(string $key = null, $default = null)
    {
        if ($key === null) return $this->query;
        return $this->query[$key] ?? $default;
    }

    public function post(string $key = null, $default = null)
    {
        if ($key === null) return $this->post;
        return $this->post[$key] ?? $default;
    }

    public function json(string $key = null, $default = null)
    {
        if ($this->jsonCache === null) {
            $this->jsonCache = [];
            $contentType = $this->header('Content-Type', '');
            if (str_contains($contentType, 'application/json') || str_contains($contentType, 'application/+json')) {
                $raw = file_get_contents('php://input');
                $decoded = json_decode($raw, true);
                $this->jsonCache = is_array($decoded) ? $decoded : [];
            }
        }
        if ($key === null) return $this->jsonCache;
        return $this->jsonCache[$key] ?? $default;
    }

    public function file(string $key = null, $default = null)
    {
        if ($key === null) return $this->files;
        return $this->files[$key] ?? $default;
    }

    public function files(): array
    {
        return $this->files;
    }

    public function header(string $key, $default = null)
    {
        $key = strtoupper(str_replace('-', '_', $key));
        if (isset($this->server['HTTP_' . $key])) {
            return $this->server['HTTP_' . $key];
        }
        if (isset($this->server[$key])) {
            return $this->server[$key];
        }
        return $default;
    }

    public function headers(): array
    {
        $headers = [];
        foreach ($this->server as $k => $v) {
            if (str_starts_with($k, 'HTTP_')) {
                $name = str_replace('_', '-', substr($k, 5));
                $headers[$name] = $v;
            }
        }
        return $headers;
    }

    public function bearerToken(): ?string
    {
        $header = $this->header('Authorization', '');
        if (preg_match('/Bearer\s+(.+)/i', $header, $m)) {
            return trim($m[1]);
        }
        return null;
    }

    public function ip(): string
    {
        return $this->server['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    public function path(): string
    {
        $uri = parse_url($this->server['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        return '/' . trim($uri ?? '/', '/');
    }

    public function segments(): array
    {
        return array_values(array_filter(explode('/', $this->path())));
    }

    public function segment(int $index, $default = null)
    {
        $segments = $this->segments();
        return $segments[$index - 1] ?? $default;
    }

    public function isAjax(): bool
    {
        return strtolower($this->header('X-Requested-With', '')) === 'xmlhttprequest';
    }

    public function expectsJson(): bool
    {
        $accept = $this->header('Accept', '');
        return str_contains($accept, 'application/json') || $this->isAjax();
    }

    /** Route parametrlari (dispatch tomonidan o'rnatiladi). */
    public function setRouteParams(array $params): void
    {
        $this->routeParams = $params;
    }

    public function routeParam(string $key, $default = null)
    {
        return $this->routeParams[$key] ?? $default;
    }

    public function routeParams(): array
    {
        return $this->routeParams;
    }

    /**
     * Validatsiya — xato bo'lsa old input flash qilinadi va
     * ValidationException throw qilinadi (Handler redirect back qiladi).
     * Muvaffaqiyatli bo'lsa validatsiyadan o'tgan maydonlar qaytariladi.
     */
    public function validate(array $rules, array $messages = []): array
    {
        $validator = new Validator($this->all(), $rules, $messages);
        if ($validator->fails()) {
            Session::instance()->flash('_errors', $validator->errors());
            Session::instance()->flash('_old_input', $this->except(['password', 'password_confirmation', '_token', '_method']));
            throw new ValidationException($validator->errors());
        }
        return $validator->validated();
    }
}