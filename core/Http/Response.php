<?php
namespace Qadamchi\Http;

/**
 * HTTP javob (instance). Laravel'ning Response g'oyasi.
 * json/redirect/make + send(). exit yo'q — testable.
 */
class Response
{
    protected $content;
    protected int $status;
    protected array $headers = [];

    public function __construct($content = '', int $status = 200, array $headers = [])
    {
        $this->content = $content;
        $this->status = $status;
        $this->headers = $headers;
    }

    public static function make($content = '', int $status = 200, array $headers = []): self
    {
        return new self($content, $status, $headers);
    }

    public static function json($data, int $status = 200, array $headers = []): self
    {
        $headers['Content-Type'] = 'application/json; charset=UTF-8';
        return new self(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $status, $headers);
    }

    public static function redirect(?string $url = null, int $status = 302): self
    {
        if ($url === null) {
            $url = $_SERVER['HTTP_REFERER'] ?? '/';
        }
        return new self('', $status, ['Location' => $url]);
    }

    public function withHeader(string $key, string $value): self
    {
        $this->headers[$key] = $value;
        return $this;
    }

    public function withStatus(int $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function withHeaders(array $headers): self
    {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    /** Flash ma'lumot qo'shadi (keyingi so'rovda yashaydi) va o'zini qaytaradi. */
    public function with(string $key, $value): self
    {
        \Qadamchi\Http\Session::instance()->flash($key, $value);
        return $this;
    }

    public function withErrors(array $errors): self
    {
        \Qadamchi\Http\Session::instance()->flash('_errors', $errors);
        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->status;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function send(): void
    {
        // Status codeni har doim o'rnatamiz (headers_sent bo'lsa ham —
        // test runner http_response_code() orqali o'qiydi; haqiqiy header
        // yuborilmasa ham qiymat so'rovi uchun to'g'ri bo'ladi).
        http_response_code($this->status);
        if (!headers_sent()) {
            foreach ($this->headers as $name => $value) {
                header("$name: $value");
            }
        }
        if ($this->content !== null && $this->content !== '') {
            echo $this->content;
        }
    }
}