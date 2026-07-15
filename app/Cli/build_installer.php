<?php
/**
 * build:installer — butun loyihani bitta install.php fayliga pakkalaydi.
 *   php qadamchi build:installer
 *
 * Source of truth = repo fayllari. install.php har doim qayta generatsiya qilinadi
 * (drift yo'q). install.php o'zini ochib, .env generatsiya qilib, (ixtiyoriy) migrate
 * ishga tushirib, o'zini o'chiradi.
 */

$base = dirname(__DIR__, 2); // loyiha ildizi
$outFile = $base . '/install.php';

// Pakkalanadigan papkalar va fayllar
$packDirs = ['core', 'app', 'bootstrap', 'config', 'routes', 'public', 'docs', 'tests', 'database', 'resources', 'lang'];
$packFiles = ['qadamchi', '.env.namuna', 'README.md', 'composer.json', '.htaccess'];

// Exclude qoidalari: papkalar prefix bilan, alohida fayllar aniqlikda.
$excludeDirPrefixes = ['storage/', 'vendor/', '.git/', 'node_modules/'];
$excludeExact = ['install.php', '.env']; // .env.namuna EMAS — u kerak!
// SQLite runtime fayllari ham pack qilinmasin — yangi o'rnatilishda bo'sh bo'lsin.
$excludeSuffixes = ['.sqlite', '.sqlite-journal', '.sqlite-wal', '.sqlite-shm', '.db'];

$manifest = [];

$exclude = function (string $rel) use ($excludeDirPrefixes, $excludeExact, $excludeSuffixes): bool {
    $rel = str_replace('\\', '/', $rel);
    foreach ($excludeDirPrefixes as $ex) {
        if (str_starts_with($rel, $ex)) return true;
    }
    if (in_array($rel, $excludeExact, true)) return true;
    foreach ($excludeSuffixes as $sfx) {
        if (str_ends_with($rel, $sfx)) return true;
    }
    return false;
};

$addFile = function (string $abs, string $rel) use (&$manifest, $exclude) {
    $rel = str_replace('\\', '/', $rel);
    if ($exclude($rel)) return;
    if (!is_file($abs)) return;
    $manifest[$rel] = file_get_contents($abs);
};

// Papkalarni yurib chiqamiz
foreach ($packDirs as $dir) {
    $absDir = $base . '/' . $dir;
    if (!is_dir($absDir)) continue;
    $rii = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($absDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($rii as $file) {
        if (!$file->isFile()) continue;
        $abs = $file->getPathname();
        $rel = substr(str_replace('\\', '/', $abs), strlen(str_replace('\\', '/', $base)) + 1);
        $addFile($abs, $rel);
    }
}
// Root fayllar
foreach ($packFiles as $f) {
    $addFile($base . '/' . $f, $f);
}

if (!$manifest) {
    echo "Pakkalanadigan fayl topilmadi — loyiha ildizida turibmisiz?\n";
    exit(1);
}

// Payload: manifest'ni serialize -> gzdeflate -> base64
$payload = base64_encode(gzdeflate(serialize($manifest), 9));
$fileCount = count($manifest);
$totalBytes = array_sum(array_map('strlen', $manifest));

// --- install.php shabloni (payload shu yerga joylanadi) ---
$installer = <<<'PHP'
<?php
/**
 * Qadamchi — bitta-fayl o'rnatuvchi (auto-generatsiya qilingan).
 * Foydalanish: php install.php
 *   yoki veb-da:  http://saying/install.php  (PHP 8+ kerak)
 *
 * Bu faylni QO'LDA TAHRIRLAMANG — `php qadamchi build:installer` qayta generatsiya qiladi.
 */

/*PAYLOAD*/

if (PHP_SAPI !== 'cli' && PHP_SAPI !== 'cli-server') {
    // veb orqali ishga tushirilgan bo'lishi mumkin
}

ini_set('display_errors', '1');
error_reporting(E_ALL);

echo "Qadamchi o'rnatuvchi\n====================\n";

// 1) Talablar
if (version_compare(PHP_VERSION, '8.0.0', '<')) {
    exit("Xato: PHP 8.0+ kerak (joriy: " . PHP_VERSION . ")\n");
}
$requiredExt = ['pdo', 'mbstring', 'json'];
foreach ($requiredExt as $ext) {
    if (!extension_loaded($ext)) {
        echo "Ogohlantirish: \"$ext\" kengaytmasi yo'q. Davom etamiz, lekin ba'zi imkoniyatlar ishlamasligi mumkin.\n";
    }
}

// 2) Payload'ni ochamiz
$manifest = unserialize(gzinflate(base64_decode($__PAYLOAD)));
if (!$manifest || !is_array($manifest)) {
    exit("Xato: payload buzilgan.\n");
}

$base = __DIR__;
echo count($manifest) . " ta fayl yozilmoqda...\n";

// 3) Fayllarni unpack
$written = 0;
foreach ($manifest as $rel => $content) {
    $path = $base . '/' . $rel;
    $dir = dirname($path);
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
    if (file_put_contents($path, $content) === false) {
        echo "  XATO yozilmadi: $rel\n";
        continue;
    }
    $written++;
}
echo "Yozildi: $written ta fayl.\n";

// 4) Runtime papkalar
foreach (['storage/logs', 'storage/framework/views', 'storage/framework/cache', 'storage/framework/sessions', 'database'] as $d) {
    if (!is_dir($base . '/' . $d)) @mkdir($base . '/' . $d, 0777, true);
    @touch($base . '/' . $d . '/.gitkeep');
}
// qadamchi'ga ijro huquqi (unix)
if (!is_windows()) @chmod($base . '/qadamchi', 0755);

// 5) .env generatsiya (.env.namuna -> .env + APP_KEY)
$envFile = $base . '/.env';
if (!file_exists($envFile) && file_exists($base . '/.env.namuna')) {
    copy($base . '/.env.namuna', $envFile);
    echo ".env yaratildi (.env.namuna asosida).\n";
}
if (file_exists($envFile)) {
    $key = 'base64:' . base64_encode(random_bytes(32));
    $lines = file($envFile, FILE_IGNORE_NEW_LINES);
    $found = false;
    foreach ($lines as $i => $line) {
        if (str_starts_with($line, 'APP_KEY=')) { $lines[$i] = 'APP_KEY=' . $key; $found = true; break; }
    }
    if (!$found) $lines[] = 'APP_KEY=' . $key;
    file_put_contents($envFile, implode("\n", $lines) . "\n");
    echo "APP_KEY generatsiya qilindi.\n";
}

echo "\nO'rnatish tugadi!\n";
echo "Keyingi qadamlar:\n";
echo "  1. .env faylida DB sozlamalari (default: SQLite, database/database.sqlite).\n";
echo "     MySQL/PostgreSQL'ga o'tish uchun DB_CONNECTION va DB_* qiymatlarini o'zgartiring.\n";
echo "  2. php qadamchi migrate        (jadval yaratish — SQLite fayli avtomatik yaratiladi)\n";
echo "  3. php qadamchi db:seed         (namuna ma'lumot)\n";
echo "  4. php qadamchi serve           (http://localhost:8080)\n";

// 6) O'zini o'chir (ixtiyoriy — .installed belgisi qo'yamiz)
@file_put_contents($base . '/.installed', date('c'));
if (PHP_SAPI === 'cli') {
    @unlink(__FILE__);
    echo "(install.php o'chirildi.)\n";
}

function is_windows(): bool { return DIRECTORY_SEPARATOR === '\\'; }
PHP;

// Payload'ni joylaymiz
$payloadVar = '$__PAYLOAD = ' . var_export($payload, true) . ';';
$installer = str_replace('/*PAYLOAD*/', $payloadVar, $installer);

file_put_contents($outFile, $installer);
$installSize = filesize($outFile);

echo "install.php generatsiya qilindi: $outFile\n";
echo "  Fayllar: $fileCount | Manba: " . round($totalBytes / 1024, 1) . " KB | install.php: " . round($installSize / 1024, 1) . " KB\n";