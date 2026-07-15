<?php
namespace App\Middlewares;

use Qadamchi\Http\Middleware;
use Qadamchi\Http\Response;
use Qadamchi\Auth\Auth;

/**
 * guest middleware — faqat kirilmagan foydalanuvchilar uchun (login/register).
 */
class GuestMiddleware extends Middleware
{
    public function handle($request, \Closure $next)
    {
        if (Auth::check()) {
            return Response::redirect('/');
        }
        return $next($request);
    }
}