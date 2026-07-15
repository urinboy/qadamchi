<?php
/**
 * Qadamchi migrate.php — migration runner (Laravel uslubidagi DB-table tracking).
 * bootstrap/cli.php orqali yuklanadi (config(), DB::connection(), Schema, aliaslar).
 *
 * Foydalanish (qadamchi router'dan):
 *   php qadamchi migrate        -> up
 *   php qadamchi migrate:rollback -> down
 *   php qadamchi migrate:reset    -> reset
 *   php qadamchi migrate:fresh    -> fresh
 * Yoki to'g'ridan-to'g'ri: php app/Cli/migrate.php [up|down|reset|fresh]
 */

require_once __DIR__ . '/../../bootstrap/cli.php';

use Qadamchi\Database\DB;

$cmd = $argv[1] ?? 'up';
// `php qadamchi migrate` (subcommandsiz) -> 'up'
if ($cmd === 'migrate') $cmd = 'up';

try {
    $pdo = DB::connection();
} catch (\Throwable $e) {
    echo "DB ulanishda xatolik: {$e->getMessage()}\n";
    exit(1);
}

function migrationTableName(): string
{
    $name = config('db.migration_table', 'migrations');
    return $name ?: 'migrations';
}

function ensureMigrationTable(PDO $pdo): void
{
    $table = migrationTableName();
    $pdo->exec("CREATE TABLE IF NOT EXISTS `$table` (
        id INT AUTO_INCREMENT PRIMARY KEY,
        migration VARCHAR(255) NOT NULL,
        batch INT NOT NULL,
        migrated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
}

function getAllMigrations(): array
{
    $files = glob(base_path('app/Migrations') . '/*.php');
    sort($files);
    $migrations = [];
    foreach ($files as $file) {
        $migrations[basename($file, '.php')] = $file;
    }
    return $migrations;
}

function getRanMigrations(PDO $pdo): array
{
    $table = migrationTableName();
    $q = $pdo->query("SELECT migration FROM `$table`");
    return $q ? $q->fetchAll(PDO::FETCH_COLUMN) : [];
}

function getLastBatch(PDO $pdo): int
{
    $table = migrationTableName();
    $q = $pdo->query("SELECT MAX(batch) FROM `$table`");
    return (int) ($q ? $q->fetchColumn() : 0);
}

function getBatchMigrations(PDO $pdo, int $batch): array
{
    $table = migrationTableName();
    $stmt = $pdo->prepare("SELECT migration FROM `$table` WHERE batch = ? ORDER BY id DESC");
    $stmt->execute([$batch]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function migrationClassName(string $filename): string
{
    $base = preg_replace('/^(\d+_)+/', '', $filename);
    return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $base)));
}

function applyMigration(string $file, string $method): bool
{
    // Migration fayli compile bo'lishidan OLDIN aliaslarni yaratamiz
    // (aks holda `function (Blueprint $t)` type-hint'i alias hali yo'qligi uchun xato beradi).
    class_exists('Schema', true);
    class_exists('Blueprint', true);
    class_exists('Migration', true);

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

// --- up mantiqi (fresh ham shuni chaqiradi) ---
function run_migrate_up(PDO $pdo): void
{
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
    if ($count === 0) echo "Barcha migrationlar bajarilgan.\n";
}

// --- up ---
if ($cmd === 'up') {
    run_migrate_up($pdo);
    exit(0);
}

// --- down (rollback oxirgi batch) ---
if ($cmd === 'down') {
    ensureMigrationTable($pdo);
    $batch = getLastBatch($pdo);
    if ($batch < 1) { echo "Rollback qilinadigan migration yo'q.\n"; exit(0); }
    $toRollback = getBatchMigrations($pdo, $batch);
    if (!$toRollback) { echo "Rollback qilinadigan migration yo'q.\n"; exit(0); }
    foreach ($toRollback as $name) {
        $file = base_path('app/Migrations/' . $name . '.php');
        if (!is_file($file)) continue;
        if (applyMigration($file, 'down')) {
            $stmt = $pdo->prepare("DELETE FROM `" . migrationTableName() . "` WHERE migration = ?");
            $stmt->execute([$name]);
            echo "Rolled back: $name\n";
        }
    }
    exit(0);
}

// --- reset (hammasini down) ---
if ($cmd === 'reset') {
    ensureMigrationTable($pdo);
    $table = migrationTableName();
    $q = $pdo->query("SELECT migration FROM `$table` ORDER BY id DESC");
    $all = $q ? $q->fetchAll(PDO::FETCH_COLUMN) : [];
    foreach ($all as $name) {
        $file = base_path('app/Migrations/' . $name . '.php');
        if (!is_file($file)) continue;
        if (applyMigration($file, 'down')) {
            $stmt = $pdo->prepare("DELETE FROM `$table` WHERE migration = ?");
            $stmt->execute([$name]);
            echo "Rolled back: $name\n";
        }
    }
    exit(0);
}

// --- fresh (barcha jadvallarni drop + qayta up) ---
if ($cmd === 'fresh') {
    // Barcha jadvallarni topamiz va drop qilamiz (Laravel migrate:fresh kabi).
    $rows = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    if ($rows) {
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
        foreach ($rows as $tbl) {
            $pdo->exec("DROP TABLE IF EXISTS `$tbl`");
            echo "Dropped table: $tbl\n";
        }
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    }
    echo "Barcha jadvallar o'chirildi.\n";
    run_migrate_up($pdo);
    exit(0);
}

echo "Noto'g'ri komanda: $cmd\n";
echo "Foydalanish: php qadamchi migrate [up|down|reset|fresh]\n";
exit(1);