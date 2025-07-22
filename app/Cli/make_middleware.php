<?php
$name = $argv[2] ?? null;
if (!$name) { 
    echo "Middleware nomini kiriting!\n"; 
    exit(1);
}

$class = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $name)));
$path = "app/Middlewares/{$class}.php";

// app/Middlewares papkasini yaratish (agar yo'q bo'lsa)
if (!is_dir('app/Middlewares')) {
    mkdir('app/Middlewares', 0777, true);
}

if (file_exists($path)) { 
    echo "Middleware mavjud: $path\n"; 
    exit(1);
}

$stub = file_get_contents(__DIR__.'/stub/middleware.stub');
$stub = str_replace('{{class}}', $class, $stub);
file_put_contents($path, $stub);
echo "Middleware yaratildi: $path\n";