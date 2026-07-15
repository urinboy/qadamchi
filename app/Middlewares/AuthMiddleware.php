<?php
namespace App\Middlewares;

use Qadamchi\Http\Middleware;
use Qadamchi\Http\Response;
use Qadamchi\Auth\Auth;

/**
 * auth middleware — kirgan foydalanuvchini talab qiladi.
 * Route::get('/profile', 'UserController@profile')->middleware('auth');
 */
class AuthMiddleware extends Middleware
{
    public function handle($request, \Closure $next)
    {
        if (!Auth::check()) {
            return Response::redirect('/login');
        }
        return $next($request);
    }
}