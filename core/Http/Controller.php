<?php
namespace Qadamchi\Http;

/**
 * Controller bazasi. view()/redirect()/back() global helperlar orqali.
 * Middleware biriktirish: $this->middleware(AuthMiddleware::class) yoki 'auth'.
 */
abstract class Controller
{
    protected array $middleware = [];

    protected function middleware($middleware, array $options = []): void
    {
        $this->middleware[] = ['class' => $middleware, 'options' => $options];
    }

    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    protected function json($data, int $status = 200): Response
    {
        return Response::json($data, $status);
    }
}