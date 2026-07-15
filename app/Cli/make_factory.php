<?php
/**
 * make:factory — yangi factory generatsiya qiladi (Database\Factories namespace).
 * php qadamchi make:factory Post -> database/factories/PostFactory.php
 */
require_once __DIR__ . '/../../bootstrap/cli.php';

$name = $argv[2] ?? null;
if (!$name) {
    echo "Factory nomini kiriting (masalan: Post)!\n";
    exit(1);
}

$class = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $name)));
$path = database_path("factories/{$class}Factory.php");

// database/factories papkasini yaratish (agar yo'q bo'lsa)
$dir = database_path('factories');
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

if (file_exists($path)) {
    echo "Factory mavjud: $path\n";
    exit(1);
}

// 'Factory' aliasi stub'dagi `extends Factory` ishlashi uchun kerak.
class_exists('Factory', true);

$stub = file_get_contents(__DIR__ . '/stub/factory.stub');
$stub = str_replace('{{class}}', $class, $stub);
file_put_contents($path, $stub);
echo "Factory yaratildi: $path\n";