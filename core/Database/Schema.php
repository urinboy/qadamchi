<?php
namespace Qadamchi\Database;

use Qadamchi\Database\DB;
use Qadamchi\Database\Grammars\SchemaGrammar;

/**
 * Schema builder — jadval yaratish/o'zgartirish/drop (Laravel'ning Schema g'oyasi).
 * Driver grammar'lar orqali ishlaydi — SQL driver-agnostik.
 */
class Schema
{
    protected static ?SchemaGrammar $grammar = null;

    /** Joriy driver bo'yicha grammar (keshlab). */
    public static function grammar(): SchemaGrammar
    {
        if (self::$grammar === null) {
            self::$grammar = SchemaGrammar::forDriver(DB::driver());
        }
        return self::$grammar;
    }

    public static function create(string $table, \Closure $callback): void
    {
        $blueprint = new Blueprint($table, self::grammar());
        $callback($blueprint);
        foreach ($blueprint->toCreateStatements() as $sql) {
            DB::statement($sql);
        }
    }

    public static function table(string $table, \Closure $callback): void
    {
        $blueprint = new Blueprint($table, self::grammar());
        $callback($blueprint);
        foreach ($blueprint->toAlterSql() as $sql) {
            DB::statement($sql);
        }
    }

    public static function drop(string $table): void
    {
        DB::statement((new Blueprint($table, self::grammar()))->toDropNoIfSql());
    }

    public static function dropIfExists(string $table): void
    {
        DB::statement((new Blueprint($table, self::grammar()))->toDropSql());
    }

    public static function hasTable(string $table): bool
    {
        $rows = DB::select(self::grammar()->hasTableSql(), [$table]);
        return !empty($rows);
    }

    public static function getColumnType(string $table, string $column): ?string
    {
        $grammar = self::grammar();
        $rows = DB::select($grammar->listColumnsSql($table), []);
        foreach ($rows as $row) {
            $field = $row['Field'] ?? $row['name'] ?? ($row['column_name'] ?? null);
            if ($field === $column) {
                return $row['Type'] ?? $row['type'] ?? ($row['data_type'] ?? null);
            }
        }
        return null;
    }

    /**
     * Barcha jadvallarni drop qilish (migrate:fresh uchun).
     * Driver grammar orqali: FK checklarni o'chirib, drop qilib, qayta yoqadi.
     */
    public static function dropAllTables(): void
    {
        $grammar = self::grammar();
        $tables = DB::select($grammar->listTablesSql(), []);
        // Birinchi ustun qiymatini olamiz — mysql `Tables_in_<db>`, sqlite `name`,
        // pgsql `tablename` (ustun nomlari har xil).
        $names = array_map(fn($row) => array_values($row)[0] ?? null, $tables);
        $names = array_filter($names, fn($n) => $n !== null && $n !== '');
        if (empty($names)) return;
        foreach ($grammar->dropAllTables($names) as $sql) {
            DB::statement($sql);
        }
    }
}