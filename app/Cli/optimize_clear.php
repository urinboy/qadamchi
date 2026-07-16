<?php
/**
 * optimize:clear — barcha keshlarni tozalaydi (Laravel'dagi kabi).
 * view + cache + config + route + session. Konfiguratsiya/route'lar hozircha
 * persisted keshsiz ishlagani uchun 0 chiqadi — bu normal.
 */
require_once __DIR__ . '/../../bootstrap/cli.php';

function clearDir(string $dir): int
{
    if (!is_dir($dir)) return 0;
    $count = 0;
    foreach (scandir($dir) as $file) {
        if ($file === '.' || $file === '..') continue;
        $path = $dir . DIRECTORY_SEPARATOR . $file;
        if (is_dir($path)) {
            $count += clearDir($path);
            @rmdir($path);
        } else {
            @unlink($path);
            $count++;
        }
    }
    return $count;
}

echo "Barcha keshlar tozalanmoqda...\n\n";

$targets = [
    'view'    => storage_path('framework/views'),
    'cache'   => storage_path('framework/cache'),
    'config'  => storage_path('framework/config'),
    'route'   => storage_path('framework/routes'),
    'session' => storage_path('framework/sessions'),
];

$total = 0;
foreach ($targets as $name => $dir) {
    $n = clearDir($dir);
    $total += $n;
    printf("  %-9s %d ta fayl\n", $name . ':', $n);
}

foreach (['config.php', 'routes.php'] as $f) {
    $p = base_path('bootstrap/cache/' . $f);
    if (is_file($p)) {
        @unlink($p);
        $total++;
        echo "  bootstrap/cache/$f o'chirildi\n";
    }
}

echo "\noptimize:clear tugadi — jami $total ta fayl tozalandi.\n";