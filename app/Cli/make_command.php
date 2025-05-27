<?php
$name = $argv[2] ?? null;
if (!$name) { echo "Command nomini kiriting!\n"; exit(1);}
$class = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $name)));
$path = "app/Commands/{$class}.php";
if (!is_dir('app/Commands')) mkdir('app/Commands', 0777, true);
$stub = file_get_contents(__DIR__.'/stub/command.stub');
$stub = str_replace('{{class}}', $class, $stub);
file_put_contents($path, $stub);
echo "Command yaratildi: $path\n";