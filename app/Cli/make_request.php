<?php
$name = $argv[2] ?? null;
if (!$name) { 
    echo "Request nomini kiriting!\n"; 
    exit(1);
}

$class = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $name)));

// Request suffix qo'shish (agar yo'q bo'lsa)
if (!str_ends_with($class, 'Request')) {
    $class .= 'Request';
}

$path = "app/Requests/{$class}.php";

// app/Requests papkasini yaratish (agar yo'q bo'lsa)
if (!is_dir('app/Requests')) {
    mkdir('app/Requests', 0777, true);
}

if (file_exists($path)) { 
    echo "Request mavjud: $path\n"; 
    exit(1);
}

$stub = file_get_contents(__DIR__.'/stub/request.stub');
$stub = str_replace('{{class}}', $class, $stub);
file_put_contents($path, $stub);
echo "Request yaratildi: $path\n";