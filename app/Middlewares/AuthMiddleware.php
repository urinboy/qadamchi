<?php
namespace App\Middlewares;

class AuthMiddleware {
    public function handle($request, $next) {
        if (!\Auth::check()) {
            header('Location: /login');
            exit;
        }
        return $next($request);
    }
}