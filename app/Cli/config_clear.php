<?php
/**
 * config:clear — config keshini tozalaydi.
 * Hozircha config'lar har so'rovda fayldan o'qiladi (persisted kesh yo'q),
 * shuning uchun bu no-op xabar beradi. Kelajakda config:cache qo'shilsa,
 * storage/framework/config va bootstrap/cache/config.php'ni tozalaydi.
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

$count = clearDir(storage_path('framework/config'));
$file = base_path('bootstrap/cache/config.php');
if (is_file($file)) {
    @unlink($file);
    $count++;
}

echo "Config keshi tozalandi — $count ta fayl o'chirildi.\n";