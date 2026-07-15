<?php
/**
 * test — mini test runner (PHPUnit'siz).
 * tests/ papkasidagi *Test.php fayllarini topib, test* metodlarini ishga tushiradi.
 *   php qadamchi test
 *   php qadamchi test ExampleTest   (bitta test)
 */
require_once __DIR__ . '/../../bootstrap/autoload.php';
require_once __DIR__ . '/../../bootstrap/app.php';

$filter = $argv[2] ?? null;
$testsDir = base_path('tests');

if (!is_dir($testsDir)) {
    echo "tests/ papkasi topilmadi. Avval `php qadamchi make:test` yoki qo'lda test yarating.\n";
    exit(0);
}

$files = glob($testsDir . '/*Test.php');
if (!$files) {
    echo "Test fayllari topilmadi (tests/*Test.php).\n";
    exit(0);
}

$passed = 0;
$failed = 0;
$total = 0;

foreach ($files as $file) {
    $class = basename($file, '.php');
    if ($filter && stripos($class, $filter) === false) continue;

    require_once $file;
    if (!class_exists($class)) {
        echo "  SKIP $class (klass topilmadi)\n";
        continue;
    }

    $reflection = new ReflectionClass($class);
    if ($reflection->isAbstract()) continue;

    $instance = new $class();
    $methods = array_filter(get_class_methods($instance), fn($m) => str_starts_with($m, 'test'));

    // protected setUp/tearDown'ni reflection bilan chaqiramiz
    $invoke = function (string $hook) use ($instance) {
        if (!method_exists($instance, $hook)) return;
        $rm = new ReflectionMethod($instance, $hook);
        $rm->setAccessible(true);
        $rm->invoke($instance);
    };

    echo "\n$class\n";
    foreach ($methods as $method) {
        $total++;
        try {
            $invoke('setUp');
            $rm = new ReflectionMethod($instance, $method);
            $rm->setAccessible(true);
            $rm->invoke($instance);
            $invoke('tearDown');
            echo "  \033[32m✔\033[0m $method\n";
            $passed++;
        } catch (\Throwable $e) {
            echo "  \033[31m✘\033[0m $method — " . $e->getMessage() . "\n";
            $failed++;
        }
    }
}

echo "\n" . str_repeat('-', 40) . "\n";
echo "Jami: $total | \033[32mO'tdi: $passed\033[0m | \033[31mYiqildi: $failed\033[0m\n";
exit($failed > 0 ? 1 : 0);