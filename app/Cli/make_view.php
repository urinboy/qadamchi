<?php
$name = $argv[2] ?? null;
if (!$name) { 
    echo "View nomini kiriting!\n"; 
    exit(1);
}

$filename = str_replace(['.', '\\', '/'], '_', $name);
$path = "app/Views/{$filename}.php";

// app/Views papkasini yaratish (agar yo'q bo'lsa)
if (!is_dir('app/Views')) {
    mkdir('app/Views', 0777, true);
}

if (file_exists($path)) { 
    echo "View mavjud: $path\n"; 
    exit(1);
}

$stub = file_get_contents(__DIR__.'/stub/view.stub');
$stub = str_replace('{{name}}', $filename, $stub);
file_put_contents($path, $stub);
echo "View yaratildi: $path\n";