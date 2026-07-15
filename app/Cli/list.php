<?php
/**
 * list — barcha mavjud qadamchi buyruqlarini ro'yxati.
 */
echo "Qadamchi CLI v" . \Qadamchi\Support\Version::VERSION . " — mavjud buyruqlar:\n\n";

$commands = [
    'migrate'              => 'Migrationlarni ishga tushirish (up)',
    'migrate:rollback'     => 'Oxirgi batch migrationni bekor qilish (down)',
    'migrate:reset'        => 'Barcha migrationlarni bekor qilish',
    'migrate:fresh'        => 'Barcha jadvallarni drop + qayta migrate',
    'db:seed'              => 'Seederlarni ishga tushirish [--class=]',
    'route:list'           => 'Ro\'yxatdan o\'tgan route\'lar jadvali',
    'key:generate'         => 'APP_KEY generatsiya + .env ga yozish',
    'make:controller'      => 'Yangi controller generatsiya',
    'make:model'           => 'Yangi model generatsiya',
    'make:migration'       => 'Yangi migration generatsiya',
    'make:seeder'          => 'Yangi seeder generatsiya',
    'make:factory'         => 'Yangi factory generatsiya',
    'make:middleware'      => 'Yangi middleware generatsiya',
    'make:request'         => 'Yangi FormRequest generatsiya',
    'make:view'            => 'Yangi Blade view generatsiya [--flat]',
    'make:test'            => 'Yangi test fayl generatsiya',
    'make:command'         => 'Yangi CLI buyruq generatsiya',
    'cache:clear'          => 'View/cache fayllarini tozalash',
    'session:clear'        => 'Sessionlarni tozalash',
    'log:clear'            => 'Log fayllarini tozalash',
    'serve'                => 'PHP built-in server (development)',
    'test'                 => 'Testlarni ishga tushirish',
    'build:installer'      => 'Bitta-fayl install.php generatsiya',
    'list'                 => 'Shu ro\'yxat',
];

foreach ($commands as $name => $desc) {
    printf("  %-22s  %s\n", $name, $desc);
}

echo "\nFoydalanish: php qadamchi <buyruq> [options]\n";