<?php
require_once __DIR__ . '/../../bootstrap/cli.php';

$name = $argv[2] ?? null;
if (!$name) { 
    echo "Migration nomini kiriting!\n"; 
    exit(1);
}

$timestamp = date('Y_m_d_His');
$class = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $name)));
$filename = "{$timestamp}_{$name}.php";
$path = database_path("migrations/{$filename}");

// database/migrations papkasini yaratish (agar yo'q bo'lsa)
$dir = database_path('migrations');
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

$stub = file_get_contents(__DIR__.'/stub/migration.stub');

// Avtomatik table nomini aniqlash (agar nom create_x_table bo'lsa)
$table = 'table_name';
if (preg_match('/create_([a-z0-9_]+)_table/', $name, $m)) {
    $table = $m[1];
}

$stub = str_replace(['{{class}}', 'table_name'], [$class, $table], $stub);
file_put_contents($path, $stub);
echo "Migration yaratildi: $path\n";