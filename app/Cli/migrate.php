<?php
/**
 * Qadamchi migrate.php
 * Migrationlarni boshqaruvchi CLI script
 */

require_once __DIR__ . '/../../core/Migration.php';

$cmd = $argv[1] ?? 'up';

$db = require __DIR__ . '/../../config/db.php';
$pdo = null;
try {
    $dsn = "mysql:host={$db['host']};dbname={$db['name']};charset=utf8mb4";
    $pdo = new PDO($dsn, $db['user'], $db['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (Exception $e) {
    echo "DB ulanishda xatolik: {$e->getMessage()}\n";
    exit(1);
}

function migrationTableName() {
    $env = __DIR__ . '/../../.env';
    if (!file_exists($env)) return 'migrations';
    $lines = file($env, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (stripos($line, 'MIGRATION_TABLE=') === 0) {
            return trim(explode('=', $line, 2)[1]);
        }
    }
    return 'migrations';
}

function ensureMigrationTable($pdo) {
    $table = migrationTableName();
    $pdo->exec("CREATE TABLE IF NOT EXISTS `$table` (
        id INT AUTO_INCREMENT PRIMARY KEY,
        migration VARCHAR(255) NOT NULL,
        batch INT NOT NULL,
        migrated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
}

function getAllMigrations() {
    $files = glob(__DIR__ . '/../../app/Migrations/*.php'); // TUZATILDI!
    sort($files);
    $migrations = [];
    foreach ($files as $file) {
        $base = basename($file, '.php');
        $migrations[$base] = $file;
    }
    return $migrations;
}

function getRanMigrations($pdo) {
    $table = migrationTableName();
    $q = $pdo->query("SELECT migration FROM `$table`");
    return $q ? $q->fetchAll(PDO::FETCH_COLUMN) : [];
}

function getLastBatch($pdo) {
    $table = migrationTableName();
    $q = $pdo->query("SELECT MAX(batch) FROM `$table`");
    return (int)($q ? $q->fetchColumn() : 0);
}

function getBatchMigrations($pdo, $batch) {
    $table = migrationTableName();
    $stmt = $pdo->prepare("SELECT migration FROM `$table` WHERE batch = ? ORDER BY id DESC");
    $stmt->execute([$batch]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function migrationClassName($filename) {
    $base = preg_replace('/^(\d+_)+/', '', $filename);
    return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $base)));
}

function applyMigration($file, $method) {
    require_once $file;
    $base = basename($file, '.php');
    $class = migrationClassName($base);
    if (!class_exists($class)) {
        echo "Migration klass topilmadi: $class ($file)\n";
        return false;
    }
    $mig = new $class();
    if (!method_exists($mig, $method)) {
        echo "Method topilmadi: $class::$method\n";
        return false;
    }
    $mig->$method();
    return true;
}

// --- Command: up ---
if ($cmd === 'up') {
    ensureMigrationTable($pdo);
    $all = getAllMigrations();
    $ran = getRanMigrations($pdo);
    $batch = getLastBatch($pdo) + 1;
    $count = 0;
    foreach ($all as $name => $file) {
        if (in_array($name, $ran)) continue;
        if (applyMigration($file, 'up')) {
            $stmt = $pdo->prepare("INSERT INTO `" . migrationTableName() . "` (migration, batch) VALUES (?, ?)");
            $stmt->execute([$name, $batch]);
            echo "Migrated: $name\n";
            $count++;
        }
    }
    if ($count == 0) echo "Barcha migrationlar bajarilgan.\n";
    exit(0);
}

// --- Command: down ---
if ($cmd === 'down') {
    ensureMigrationTable($pdo);
    $batch = getLastBatch($pdo);
    if ($batch < 1) { echo "Rollback qilinadigan migration yo'q.\n"; exit(0);}
    $toRollback = getBatchMigrations($pdo, $batch);
    if (!$toRollback) { echo "Rollback qilinadigan migration yo'q.\n"; exit(0);}
    foreach ($toRollback as $name) {
        $file = __DIR__ . '/../../app/Migrations/' . $name . '.php';
        if (!file_exists($file)) continue;
        if (applyMigration($file, 'down')) {
            $stmt = $pdo->prepare("DELETE FROM `" . migrationTableName() . "` WHERE migration = ?");
            $stmt->execute([$name]);
            echo "Rolled back: $name\n";
        }
    }
    exit(0);
}

// --- Command: reset ---
if ($cmd === 'reset') {
    ensureMigrationTable($pdo);
    $table = migrationTableName();
    $q = $pdo->query("SELECT migration FROM `$table` ORDER BY id DESC");
    $all = $q ? $q->fetchAll(PDO::FETCH_COLUMN) : [];
    foreach ($all as $name) {
        $file = __DIR__ . '/../../app/Migrations/' . $name . '.php';
        if (!file_exists($file)) continue;
        if (applyMigration($file, 'down')) {
            $stmt = $pdo->prepare("DELETE FROM `$table` WHERE migration = ?");
            $stmt->execute([$name]);
            echo "Rolled back: $name\n";
        }
    }
    exit(0);
}

// --- Command: fresh ---
if ($cmd === 'fresh') {
    $table = migrationTableName();
    $pdo->exec("DROP TABLE IF EXISTS `$table`");
    echo "Migration table dropped.\n";
    // up'ni qayta chaqirish
    $_SERVER['argv'][1] = 'up';
    require __FILE__;
    exit(0);
}

// --- Unknown command ---
echo "Noto'g'ri komanda: $cmd\n";
echo "To'g'ri foydalanish: php cli/migrate.php [up|down|reset|fresh]\n";
exit(1);