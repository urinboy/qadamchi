<?php
/**
 * Qadamchi migrate.php — migration runner (Laravel uslubidagi DB-table tracking).
 * bootstrap/cli.php orqali yuklanadi (config(), DB::connection(), Schema, aliaslar).
 *
 * Driver-agnostik: SQLite / MySQL / PostgreSQL bilan ishlaydi (grammar orqali).
 *
 * Foydalanish (qadamchi router'dan):
 *   php qadamchi migrate         -> up
 *   php qadamchi migrate:rollback -> down
 *   php qadamchi migrate:reset    -> reset
 *   php qadamchi migrate:fresh    -> fresh
 * Yoki to'g'ridan-to'g'ri: php app/Cli/migrate.php [up|down|reset|fresh]
 */

require_once __DIR__ . '/../../bootstrap/cli.php';

use Qadamchi\Database\DB;
use Qadamchi\Database\Schema;
use Qadamchi\Database\Blueprint;

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

/** Migration tracking jadvali nomini grammar quote qilgan holda qaytaradi. */
function migrationTableWrapped(): string
{
    return Schema::grammar()->wrap(migrationTableName());
}

function ensureMigrationTable(PDO $pdo): void
{
    $table = migrationTableName();
    if (Schema::hasTable($table)) return;
    Schema::create($table, function (Blueprint $t) {
        $t->id();
        $t->string('migration');
        $t->integer('batch');
        $t->timestamp('migrated_at')->nullable();
    });
}

function getAllMigrations(): array
{
    $files = glob(database_path('migrations') . '/*.php');
    sort($files);
    $migrations = [];
    foreach ($files as $file) {
        $migrations[basename($file, '.php')] = $file;
    }
    return $migrations;
}

function getRanMigrations(PDO $pdo): array
{
    $table = migrationTableWrapped();
    $rows = DB::select("SELECT migration FROM $table");
    return array_map(fn($r) => $r['migration'], $rows);
}

function getLastBatch(PDO $pdo): int
{
    $table = migrationTableWrapped();
    $rows = DB::select("SELECT MAX(batch) AS max_batch FROM $table");
    return (int) ($rows[0]['max_batch'] ?? 0);
}

function getBatchMigrations(PDO $pdo, int $batch): array
{
    $table = migrationTableWrapped();
    $rows = DB::select("SELECT migration FROM $table WHERE batch = ? ORDER BY id DESC", [$batch]);
    return array_map(fn($r) => $r['migration'], $rows);
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

/** Migration yozuvi — driver-agnostik (grammar quote). */
function recordMigration(string $name, int $batch): void
{
    $table = migrationTableWrapped();
    DB::statement("INSERT INTO $table (migration, batch) VALUES (?, ?)", [$name, $batch]);
}

function forgetMigration(string $name): void
{
    $table = migrationTableWrapped();
    DB::statement("DELETE FROM $table WHERE migration = ?", [$name]);
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
            recordMigration($name, $batch);
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
        $file = database_path('migrations/' . $name . '.php');
        if (!is_file($file)) continue;
        if (applyMigration($file, 'down')) {
            forgetMigration($name);
            echo "Rolled back: $name\n";
        }
    }
    exit(0);
}

// --- reset (hammasini down) ---
if ($cmd === 'reset') {
    ensureMigrationTable($pdo);
    $table = migrationTableWrapped();
    $rows = DB::select("SELECT migration FROM $table ORDER BY id DESC");
    $all = array_map(fn($r) => $r['migration'], $rows);
    foreach ($all as $name) {
        $file = database_path('migrations/' . $name . '.php');
        if (!is_file($file)) continue;
        if (applyMigration($file, 'down')) {
            forgetMigration($name);
            echo "Rolled back: $name\n";
        }
    }
    exit(0);
}

// --- fresh (barcha jadvallarni drop + qayta up) ---
if ($cmd === 'fresh') {
    Schema::dropAllTables();
    echo "Barcha jadvallar o'chirildi.\n";
    run_migrate_up($pdo);
    exit(0);
}

echo "Noto'g'ri komanda: $cmd\n";
echo "Foydalanish: php qadamchi migrate [up|down|reset|fresh]\n";
exit(1);