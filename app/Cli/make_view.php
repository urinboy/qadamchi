<?php
/**
 * make:view — yangi Blade view generatsiya qiladi (.blade.php, layout bilan).
 * php qadamchi make:view posts/show      -> resources/views/posts/show.blade.php
 * php qadamchi make:view home --flat     -> resources/views/home.blade.php (layoutsiz)
 */
require_once __DIR__ . '/../../bootstrap/cli.php';

$name = $argv[2] ?? null;
if (!$name) {
    echo "View nomini kiriting (masalan: posts/show)!\n";
    exit(1);
}

$flat = in_array('--flat', array_slice($argv, 3), true);
$name = str_replace('\\', '/', $name);
$path = resource_path("views/{$name}.blade.php");

$dir = dirname($path);
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}
if (file_exists($path)) {
    echo "View mavjud: $path\n";
    exit(1);
}

$viewName = basename($name);

if ($flat) {
    $stub = "<!-- {{name}} view -->\n<h1>{{ \$title ?? '{{name}}' }}</h1>\n";
} else {
    $stub = "@extends('layouts.app')\n\n@section('content')\n    <h1>{{ \$title ?? '{{name}}' }}</h1>\n@endsection\n";
}

$stub = str_replace('{{name}}', $viewName, $stub);
file_put_contents($path, $stub);
echo "View yaratildi: $path\n";
echo "Foydalanish: return view('{$name}', [...]);\n";