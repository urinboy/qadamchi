<?php
/**
 * make:test — yangi test fayl generatsiya qiladi (tests/ papkasiga).
 * php qadamchi make:test UserTest
 */
$name = $argv[2] ?? null;
if (!$name) {
    echo "Test nomini kiriting (masalan: UserTest)!\n";
    exit(1);
}

$class = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $name)));
if (!str_ends_with($class, 'Test')) {
    $class .= 'Test';
}

$path = "tests/{$class}.php";
if (!is_dir('tests')) {
    mkdir('tests', 0777, true);
}
if (file_exists($path)) {
    echo "Test mavjud: $path\n";
    exit(1);
}

$stub = file_get_contents(__DIR__ . '/stub/test.stub');
$stub = str_replace('{{class}}', $class, $stub);
file_put_contents($path, $stub);
echo "Test yaratildi: $path\n";
echo "Ishga tushirish: php qadamchi test\n";