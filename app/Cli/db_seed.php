<?php
/**
 * db:seed — seederlarni ishga tushirish (Database\Seeders namespace, PSR-4 autoload).
 * php qadamchi db:seed                   -> Database\Seeders\DatabaseSeeder
 * php qadamchi db:seed --class=UserSeeder -> Database\Seeders\UserSeeder (qisqa nom)
 * php qadamchi db:seed --class=\App\...   -> to'liq FQCN
 */
require_once __DIR__ . '/../../bootstrap/cli.php';

$class = 'DatabaseSeeder';
foreach (array_slice($argv, 2) as $arg) {
    if (str_starts_with($arg, '--class=')) {
        $class = substr($arg, 8);
    }
}

// Qisqa nom -> Database\Seeders\ FQCN (faqat \ bilan boshlanmagan va namespace yo'q bo'lsa).
if (!str_starts_with($class, '\\') && !str_contains($class, '\\')) {
    $class = 'Database\\Seeders\\' . $class;
}
$class = ltrim($class, '\\');

// 'Seeder' aliasi seeder fayllari compile bo'lishidan OLDIN kerak (`extends Seeder`).
class_exists('Seeder', true);

// PSR-4 autoload orqali yuklanadi (Database\Seeders\ -> database/seeders/).
if (!class_exists($class, true)) {
    echo "Seeder klass topilmadi: $class\n";
    exit(1);
}

(new $class())->run();
echo "Seeded: $class\n";