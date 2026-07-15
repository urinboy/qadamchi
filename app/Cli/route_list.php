<?php
/**
 * route:list — ro'yxatdan o'tgan route'larni jadval ko'rinishida.
 * routes/web.php va api.php ni yuklash uchun to'liq web bootstrap kerak.
 */
require_once __DIR__ . '/../../bootstrap/autoload.php';
require_once __DIR__ . '/../../bootstrap/app.php';

use Qadamchi\Routing\Route;

$routes = Route::routes();

if (!$routes) {
    echo "Route'lar topilmadi.\n";
    exit(0);
}

printf("%-8s  %-24s  %-40s  %s\n", 'METHOD', 'URI', 'ACTION', 'MIDDLEWARE');
echo str_repeat('-', 90) . "\n";

foreach ($routes as $r) {
    $method = is_array($r['methods']) ? implode('|', $r['methods']) : ($r['methods'] ?? '?');
    $action = $r['action'] ?? '?';
    if (is_array($action)) {
        $action = ($action[0] ?? '') . '@' . ($action[1] ?? '');
    }
    $mw = is_array($r['middleware'] ?? null) ? implode(',', $r['middleware']) : ($r['middleware'] ?? '');
    printf("%-8s  %-24s  %-40s  %s\n", $method, $r['uri'] ?? '?', $action, $mw);
}