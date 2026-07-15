<?php
/**
 * key:generate — APP_KEY generatsiya qiladi va .env ga yozadi.
 * Laravel'ga o'xshash: tasodifiy base64 kalit.
 */
require_once __DIR__ . '/../../bootstrap/cli.php';

$envFile = base_path('.env');
$namuna = base_path('.env.namuna');

if (!is_file($envFile)) {
    if (is_file($namuna)) {
        copy($namuna, $envFile);
        echo ".env.namuna -> .env nusxalandi.\n";
    } else {
        file_put_contents($envFile, "APP_KEY=\n");
    }
}

$key = 'base64:' . base64_encode(random_bytes(32));
$contents = file_get_contents($envFile);
$lines = file($envFile, FILE_IGNORE_NEW_LINES);

$found = false;
foreach ($lines as $i => $line) {
    if (str_starts_with($line, 'APP_KEY=')) {
        $lines[$i] = 'APP_KEY=' . $key;
        $found = true;
        break;
    }
}
if (!$found) {
    $lines[] = 'APP_KEY=' . $key;
}
file_put_contents($envFile, implode("\n", $lines) . "\n");

echo "APP_KEY yaratildi va .env ga yozildi: $key\n";