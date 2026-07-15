<?php
namespace App\Middlewares;

use Qadamchi\Http\Middleware;
use Qadamchi\Http\CSRF;
use Qadamchi\Http\Response;

/**
 * CSRF middleware — POST/PUT/PATCH/DELETE so'rovlarida token tekshiradi.
 * bootstrap/app.php da global middleware sifatida yoqilgan (GET o'tkazib yuboradi).
 */
class VerifyCSRF extends Middleware
{
    public function handle($request, \Closure $next)
    {
        $method = $request->method();

        if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            $token = $request->input('_token');
            if (!CSRF::validateToken($token)) {
                http_response_code(419);
                return Response::make('CSRF token mos emas (419).', 419);
            }
        }

        return $next($request);
    }
}