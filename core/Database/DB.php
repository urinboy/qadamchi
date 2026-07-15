<?php
namespace Qadamchi\Database;

use PDO;
use PDOException;

/**
 * DB — PDO singleton (Laravel'ning DB facade g'oyasi).
 * config/db.php dan o'qiydi. Driver-aware DSN: sqlite/mysql/pgsql.
 *
 * Default (Laravel 13 uslubida) — SQLite: database/database.sqlite
 * avtomatik yaratiladi. MySQL/PostgreSQL'ga .env orqali o'tiladi.
 */
class DB
{
    protected static ?PDO $pdo = null;
    protected static ?string $driver = null;

    /** Joriy driver nomi (sqlite | mysql | pgsql). */
    public static function driver(): string
    {
        if (self::$driver === null) {
            self::$driver = strtolower((string) config('db.driver', 'sqlite'));
        }
        return self::$driver;
    }

    public static function connection(): PDO
    {
        if (self::$pdo === null) {
            $config = config('db', []);
            $driver = self::driver();
            self::$pdo = self::makeConnection($driver, $config);
        }
        return self::$pdo;
    }

    protected static function makeConnection(string $driver, array $config): PDO
    {
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        if ($driver === 'sqlite') {
            $path = self::resolveSqlitePath($config);
            if ($path !== ':memory:') {
                $dir = dirname($path);
                if (!is_dir($dir)) @mkdir($dir, 0777, true);
                if (!file_exists($path)) @touch($path);
            }
            $pdo = new PDO('sqlite:' . $path, null, null, $options);
            // FK cheklovlarini yoqish (Laravel default).
            if (($config['foreign_keys'] ?? true)) {
                $pdo->exec('PRAGMA foreign_keys = ON');
            }
            return $pdo;
        }

        // mysql / pgsql
        $host = $config['host'] ?? '127.0.0.1';
        $port = $config['port'] ?? '';
        $name = $config['name'] ?? 'qadamchi';
        $user = $config['user'] ?? 'root';
        $pass = $config['pass'] ?? '';

        $dsn = $driver . ':host=' . $host;
        if ($port !== '' && $port !== null) $dsn .= ';port=' . $port;
        $dsn .= ';dbname=' . $name;
        if ($driver === 'mysql') {
            $charset = $config['charset'] ?? 'utf8mb4';
            if ($charset !== '') $dsn .= ';charset=' . $charset;
            $options[PDO::ATTR_EMULATE_PREPARES] = false;
        }

        return new PDO($dsn, $user, $pass, $options);
    }

    /** SQLite fayl yo'lini hal qiladi: :memory:, abs yo'l, yoki default database/database.sqlite. */
    protected static function resolveSqlitePath(array $config): string
    {
        $db = (string) ($config['name'] ?? '');
        if ($db === ':memory:') return ':memory:';
        if ($db === '') return database_path('database.sqlite');
        // Ko'rsatilgan qiymat yo'lga o'xshasa (separator yoki kengaytma bor) — shuni ishlatamiz.
        $looksLikePath = preg_match('#[/\\\\]#', $db) === 1 || preg_match('/\.(sqlite3?|db)$/i', $db) === 1;
        return $looksLikePath ? $db : database_path($db . '.sqlite');
    }

    public static function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = self::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function select(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }

    public static function insert(string $sql, array $params = []): int
    {
        self::query($sql, $params);
        return (int) self::connection()->lastInsertId();
    }

    public static function update(string $sql, array $params = []): int
    {
        return self::query($sql, $params)->rowCount();
    }

    public static function delete(string $sql, array $params = []): int
    {
        return self::query($sql, $params)->rowCount();
    }

    public static function statement(string $sql, array $params = []): void
    {
        self::query($sql, $params);
    }

    public static function table(string $name): QueryBuilder
    {
        return (new QueryBuilder())->from($name);
    }

    public static function transaction(\Closure $callback)
    {
        self::beginTransaction();
        try {
            $result = $callback();
            self::commit();
            return $result;
        } catch (\Throwable $e) {
            self::rollBack();
            throw $e;
        }
    }

    public static function beginTransaction(): void { self::connection()->beginTransaction(); }
    public static function commit(): void           { self::connection()->commit(); }
    public static function rollBack(): void         { self::connection()->rollBack(); }

    /** Bog'lanishni qayta o'rnatish (config o'zgargandan keyin — testlar uchun). */
    public static function flush(): void
    {
        self::$pdo = null;
        self::$driver = null;
    }
}