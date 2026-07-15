<?php
namespace Qadamchi\Database;

use PDO;
use PDOException;

/**
 * DB — PDO singleton (Laravel'ning DB facade g'oyasi).
 * config/db.php dan o'qiydi. Transactionlar bilan.
 */
class DB
{
    protected static ?PDO $pdo = null;

    public static function connection(): PDO
    {
        if (self::$pdo === null) {
            $config = config('db', []);
            $driver = $config['driver'] ?? 'mysql';
            $host = $config['host'] ?? '127.0.0.1';
            $port = $config['port'] ?? 3306;
            $name = $config['name'] ?? 'qadamchi';
            $charset = $config['charset'] ?? 'utf8mb4';

            $dsn = "$driver:host=$host;port=$port;dbname=$name;charset=$charset";
            self::$pdo = new PDO($dsn, $config['user'] ?? 'root', $config['pass'] ?? '', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }
        return self::$pdo;
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
}