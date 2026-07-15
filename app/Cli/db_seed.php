<?php
/**
 * db:seed — seederlarni ishga tushirish.
 * php qadamchi db:seed                  -> DatabaseSeeder
 * php qadamchi db:seed --class=UserSeeder -> bitta seeder
 */
require_once __DIR__ . '/../../bootstrap/cli.php';

$class = 'DatabaseSeeder';
foreach (array_slice($argv, 2) as $arg) {
    if (str_starts_with($arg, '--class=')) {
        $class = substr($arg, 8);
    }
}

$file = base_path('app/Seeders/' . $class . '.php');
if (!is_file($file)) {
    echo "Seeder topilmadi: $class ($file)\n";
    exit(1);
}

// 'Seeder' aliasi seeder fayllari compile bo'lishidan OLDIN kerak (`extends Seeder`).
class_exists('Seeder', true);

// Seederlar global namespace'da (PSR-4 emas) — shu sababli barchasini oldindan require qilamiz,
// chunki DatabaseSeeder->call('UserSeeder') boshqa seeder'ni chaqiradi.
foreach (glob(base_path('app/Seeders') . '/*.php') as $seederFile) {
    require_once $seederFile;
}

if (!class_exists($class)) {
    echo "Seeder klass topilmadi: $class\n";
    exit(1);
}

(new $class())->run();
echo "Seeded: $class\n";