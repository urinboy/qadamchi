<?php
$name = $argv[2] ?? null;
if (!$name) { 
    echo "Model nomini kiriting!\n"; 
    exit(1);
}

$class = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $name)));
$path = "app/Models/{$class}.php";

// app/Models papkasini yaratish (agar yo'q bo'lsa)
if (!is_dir('app/Models')) {
    mkdir('app/Models', 0777, true);
}

if (file_exists($path)) { 
    echo "Model mavjud: $path\n"; 
    exit(1);
}

$stub = file_get_contents(__DIR__.'/stub/model.stub');
$stub = str_replace('{{class}}', $class, $stub);
file_put_contents($path, $stub);
echo "Model yaratildi: $path\n";