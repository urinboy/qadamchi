<?php
$name = $argv[2] ?? null;
if (!$name) { 
    echo "Seeder nomini kiriting!\n"; 
    exit(1);
}

$class = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $name)));
$path = "app/Seeders/{$class}.php";

// app/Seeders papkasini yaratish (agar yo'q bo'lsa)
if (!is_dir('app/Seeders')) {
    mkdir('app/Seeders', 0777, true);
}

if (file_exists($path)) { 
    echo "Seeder mavjud: $path\n"; 
    exit(1);
}

$stub = file_get_contents(__DIR__.'/stub/seeder.stub');
$stub = str_replace('{{class}}', $class, $stub);
file_put_contents($path, $stub);
echo "Seeder yaratildi: $path\n";