<?php
function clearDir($dir) {
    if (!is_dir($dir)) return;
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        $path = "$dir/$file";
        if (is_dir($path)) {
            clearDir($path);
            @rmdir($path);
        } else {
            @unlink($path);
        }
    }
}
clearDir('storage/cache');
echo "Kesh tozalandi!\n";