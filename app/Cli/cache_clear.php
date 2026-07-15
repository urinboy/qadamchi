<?php
/**
 * cache:clear — view cache va kesh fayllarini tozalaydi.
 * storage/framework/views va storage/framework/cache.
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

$views = clearDir(storage_path('framework/views'));
$cache = clearDir(storage_path('framework/cache'));

echo "Kesh tozalandi — view: $views, cache: $cache ta fayl.\n";