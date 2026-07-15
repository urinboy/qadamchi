<?php
/**
 * Qadamchi — web kirish nuqtasi (front controller).
 * Static fayllarni PHP server'ga topshiradi, so'ngra Route::dispatch.
 */

// PHP built-in server uchun static fayl shortcut
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$publicPath = __DIR__ . $uri;
if (php_sapi_name() === 'cli-server' && is_file($publicPath)) {
    return false;
}

require __DIR__ . '/../bootstrap/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';

use Qadamchi\Http\Request;
use Qadamchi\Routing\Route;

$request = Request::instance();
$response = Route::dispatch($request);
$response->send();