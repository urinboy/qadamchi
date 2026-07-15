<?php
/**
 * Qadamchi PSR-4 autoloader (Composer'siz).
 *
 * Bu fayl A versiyasida ishlaydi. B versiyasiga o'tilganda:
 *   1) composer.json ga quyidagi PSR-4 map qo'shiladi:
 *        "Qadamchi\\": "core/", "App\\": "app/"
 *   2) require __DIR__.'/autoload.php' -> require __DIR__.'/../vendor/autoload.php'
 *   3) App va yadro kodi o'zgarmaydi.
 */

spl_autoload_register(function ($class) {
    $prefixes = [
        'Qadamchi\\'          => __DIR__ . '/../core/',
        'App\\'               => __DIR__ . '/../app/',
        'Database\\Seeders\\' => __DIR__ . '/../database/seeders/',
        'Database\\Factories\\' => __DIR__ . '/../database/factories/',
    ];

    foreach ($prefixes as $prefix => $base) {
        $len = strlen($prefix);
        if (strncmp($class, $prefix, $len) === 0) {
            $relative = substr($class, $len);
            $file = $base . str_replace('\\', '/', $relative) . '.php';
            if (is_file($file)) {
                require $file;
                return;
            }
        }
    }
});

/**
 * Qisqa aliaslar (Laravel "facade" g'oyasi).
 * `Route::get(...)` -> aslida `Qadamchi\Routing\Route::get(...)` ni chaqiradi.
 * Lazy: faqat qisqa nom ishlatilganda haqiqiy sinf yuklanadi va alias ro'yxatdan o'tadi.
 */
$aliases = require __DIR__ . '/aliases.php';

spl_autoload_register(function ($class) use ($aliases) {
    if (isset($aliases[$class]) && class_exists($aliases[$class], true)) {
        // 3-param false: alias nomini qayta autoload qilishni to'xtatamiz
        // (aks holda re-entrant autoload zanjiri class_alias'ni bekor qiladi).
        class_alias($aliases[$class], $class, false);
    }
});