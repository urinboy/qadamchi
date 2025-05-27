<?php
$name = $argv[2] ?? null;
if (!$name) { echo "Model nomini kiriting!\n"; exit(1);}
$class = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $name)));
$path = "app/Models/{$class}.php";
if (file_exists($path)) { echo "Model mavjud: $path\n"; exit(1);}
$stub = file_get_contents(__DIR__.'/stub/model.stub');
$stub = str_replace('{{class}}', $class, $stub);
file_put_contents($path, $stub);
echo "Model yaratildi: $path\n";