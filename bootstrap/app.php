<?php
/**
 * Qadamchi bootstrap — ilovani ishga tayyorlaydi (web).
 * Container, Config, Logger, Request, error handler, Route sozlamalari, route'lar.
 */
require_once __DIR__ . '/../core/Support/env.php';
load_env();
require_once __DIR__ . '/../core/Support/helpers.php';

use Qadamchi\Container\Container;
use Qadamchi\Support\Config;
use Qadamchi\Support\Logger;
use Qadamchi\Http\Request;
use Qadamchi\Routing\Route;
use Qadamchi\Exceptions\Handler;

$container = new Container();
Container::setInstance($container);

// Core service'lar (singleton)
$container->singleton(Config::class, fn() => new Config(base_path('config')));
$container->singleton(Logger::class, fn() => new Logger(storage_path('logs/qadamchi.log')));

// Request'ni bir marta capture qilamiz (DI uchun)
$request = Request::capture();
$container->instance(Request::class, $request);

// Error/exception handler
Handler::setLogger($container->make(Logger::class));
Handler::register();

// Route sozlamalari
Route::setContainer($container);
Route::middlewareAlias([
    'auth'  => \App\Middlewares\AuthMiddleware::class,
    'guest' => \App\Middlewares\GuestMiddleware::class,
    'csrf'  => \App\Middlewares\VerifyCSRF::class,
]);
// Web route'larda CSRF global yoqilgan (GET o'tkazib yuboradi)
Route::setGlobalMiddleware([\App\Middlewares\VerifyCSRF::class]);

// Route'larni yuklaymiz
require base_path('routes/web.php');
if (is_file(base_path('routes/api.php'))) {
    require base_path('routes/api.php');
}

return $container;