<?php
/**
 * session:clear — session saqlash joyini tozalaydi.
 * File driver uchun storage/framework/sessions fayllarini o'chiradi.
 */
require_once __DIR__ . '/../../bootstrap/cli.php';

$dir = storage_path('framework/sessions');
if (!is_dir($dir)) {
    echo "Session papkasi topilmadi: $dir\n";
    exit(0);
}

$count = 0;
foreach (glob($dir . '/*') as $file) {
    if (is_file($file)) {
        unlink($file);
        $count++;
    }
}
echo "Sessionlar tozalandi: $count ta fayl o'chirildi.\n";