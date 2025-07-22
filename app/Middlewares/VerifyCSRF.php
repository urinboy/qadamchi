<?php
namespace App\Middlewares;

use Middleware;
use CSRF;

class VerifyCSRF extends Middleware {
    public function handle($request, $next) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST[CSRF::TOKEN_NAME] ?? null;
            
            if (!CSRF::validateToken($token)) {
                http_response_code(419);
                echo "CSRF token mismatch";
                exit;
            }
        }
        
        return $next($request);
    }
}