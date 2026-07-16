<?php
/**
 * view:clear — compiled Blade view'larini tozalaydi (storage/framework/views).
 * Laravel'dagi kabi: keyingi so'rovda view'lar qayta compile qilinadi.
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

echo "View keshi tozalandi — $views ta compiled fayl o'chirildi.\n";