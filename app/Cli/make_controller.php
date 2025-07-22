<?php
$name = $argv[2] ?? null;
if (!$name) { 
    echo "Controller nomini kiriting!\n"; 
    exit(1);
}

$class = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $name)));
$path = "app/Controllers/{$class}.php";

// app/Controllers papkasini yaratish (agar yo'q bo'lsa)
if (!is_dir('app/Controllers')) {
    mkdir('app/Controllers', 0777, true);
}

if (file_exists($path)) { 
    echo "Controller mavjud: $path\n"; 
    exit(1);
}

$stub = file_get_contents(__DIR__.'/stub/controller.stub');
$stub = str_replace('{{class}}', $class, $stub);
file_put_contents($path, $stub);
echo "Controller yaratildi: $path\n";