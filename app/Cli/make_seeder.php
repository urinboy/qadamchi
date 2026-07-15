<?php
/**
 * make:seeder — yangi seeder generatsiya qiladi (Database\Seeders namespace).
 * php qadamchi make:seeder PostSeeder -> database/seeders/PostSeeder.php
 */
require_once __DIR__ . '/../../bootstrap/cli.php';

$name = $argv[2] ?? null;
if (!$name) {
    echo "Seeder nomini kiriting!\n";
    exit(1);
}

$class = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $name)));
$path = database_path("seeders/{$class}.php");

// database/seeders papkasini yaratish (agar yo'q bo'lsa)
$dir = database_path('seeders');
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

if (file_exists($path)) {
    echo "Seeder mavjud: $path\n";
    exit(1);
}

// 'Seeder' aliasi stub'dagi `extends Seeder` ishlashi uchun kerak.
class_exists('Seeder', true);

$stub = file_get_contents(__DIR__.'/stub/seeder.stub');
$stub = str_replace('{{class}}', $class, $stub);
file_put_contents($path, $stub);
echo "Seeder yaratildi: $path\n";