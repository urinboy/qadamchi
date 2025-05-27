<?php
$name = $argv[2] ?? null;
if (!$name) { echo "Middleware nomini kiriting!\n"; exit(1);}
$class = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $name)));
$path = "app/Middlewares/{$class}.php";
$stub = file_get_contents(__DIR__.'/stub/middleware.stub');
$stub = str_replace('{{class}}', $class, $stub);
file_put_contents($path, $stub);
echo "Middleware yaratildi: $path\n";