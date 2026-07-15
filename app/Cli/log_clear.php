<?php
/**
 * log:clear — log fayllarini tozalaydi (storage/logs).
 */
require_once __DIR__ . '/../../bootstrap/cli.php';

$dir = storage_path('logs');
if (!is_dir($dir)) {
    echo "Log papkasi topilmadi: $dir\n";
    exit(0);
}

$count = 0;
foreach (glob($dir . '/*.log') as $file) {
    if (is_file($file)) {
        unlink($file);
        $count++;
    }
}
echo "Loglar tozalandi: $count ta fayl o'chirildi.\n";