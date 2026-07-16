<?php
/**
 * route:clear — route keshini tozalaydi.
 * Hozircha route'lar har so'rovda routes/web.php'dan o'qiladi (persisted kesh yo'q),
 * shuning uchun bu no-op xabar beradi. Kelajakda route:cache qo'shilsa,
 * storage/framework/routes va bootstrap/cache/routes.php'ni tozalaydi.
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

$count = clearDir(storage_path('framework/routes'));
$file = base_path('bootstrap/cache/routes.php');
if (is_file($file)) {
    @unlink($file);
    $count++;
}

echo "Route keshi tozalandi — $count ta fayl o'chirildi.\n";