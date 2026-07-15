<?php
namespace Qadamchi\Database;

use Qadamchi\Database\DB;

/**
 * Schema builder — jadval yaratish/o'zgartirish/drop (Laravel'ning Schema g'oyasi).
 * DB::connection() ishlatadi (alohid PDO emas), utf8mb4.
 */
class Schema
{
    public static function create(string $table, \Closure $callback): void
    {
        $blueprint = new Blueprint($table);
        $callback($blueprint);
        DB::statement($blueprint->toCreateSql());
    }

    public static function table(string $table, \Closure $callback): void
    {
        $blueprint = new Blueprint($table);
        $callback($blueprint);
        foreach ($blueprint->toAlterSql() as $sql) {
            DB::statement($sql);
        }
    }

    public static function drop(string $table): void
    {
        DB::statement((new Blueprint($table))->toDropSql());
    }

    public static function dropIfExists(string $table): void
    {
        DB::statement((new Blueprint($table))->toDropSql());
    }

    public static function hasTable(string $table): bool
    {
        $name = config('db.name', 'qadamchi');
        return DB::select("SHOW TABLES LIKE ?", [$table]) ? true : false;
    }

    public static function getColumnType(string $table, string $column): ?string
    {
        $rows = DB::select("SHOW COLUMNS FROM `$table` WHERE Field = ?", [$column]);
        return $rows[0]['Type'] ?? null;
    }
}