<?php
namespace Qadamchi\Database\Grammars;

use Qadamchi\Database\Blueprint;
use Qadamchi\Database\ColumnDefinition;

/**
 * SQLite grammar — zero-config default driver (Laravel 13 uslubida).
 *
 * Double-quote identifikatorlar, INTEGER PRIMARY KEY AUTOINCREMENT, BOOLEAN=INTEGER,
 * TEXT (mediumText/longText/json), ENUM→TEXT CHECK(...), ENGINE/CHARSET/UNSIGNED yo'q,
 * indexlar alohida CREATE [UNIQUE] INDEX statementlari, sqlite_master/PRAGMA.
 */
class SQLiteGrammar extends SchemaGrammar
{
    public function wrap(string $name): string
    {
        return '"' . str_replace('"', '""', $name) . '"';
    }

    protected function typeSql(ColumnDefinition $c): string
    {
        switch ($c->type) {
            case 'bigIncrements':
            case 'increments':
                // SQLite: rowid alias — INTEGER PRIMARY KEY AUTOINCREMENT.
                return 'INTEGER PRIMARY KEY AUTOINCREMENT';
            case 'string':        return 'VARCHAR(' . (int) $c->params['length'] . ')';
            case 'text':
            case 'mediumText':
            case 'longText':      return 'TEXT';
            case 'integer':       return 'INTEGER';
            case 'bigInteger':    return 'BIGINT';
            case 'smallInteger':  return 'SMALLINT';
            case 'boolean':       return 'INTEGER'; // 0/1
            case 'float':         return 'FLOAT';
            case 'decimal':       return 'DECIMAL(' . (int) $c->params['precision'] . ',' . (int) $c->params['scale'] . ')';
            case 'enum':          return 'TEXT'; // CHECK pastda
            case 'json':          return 'TEXT';
            case 'date':          return 'DATE';
            case 'time':          return 'TIME';
            case 'datetime':      return 'DATETIME';
            case 'timestamp':     return 'DATETIME';
            case 'foreignId':     return 'INTEGER'; // FK — bigint'ga mos
            default:              return 'VARCHAR(255)';
        }
    }

    protected function primaryKeyAppended(): bool { return false; } // TYPE ichida

    public function compileColumn(ColumnDefinition $c): string
    {
        $sql = parent::compileColumn($c);
        // SQLite ENUM yo'q — CHECK constraint qo'shamiz.
        if ($c->type === 'enum' && !empty($c->params['allowed'])) {
            $list = $this->quoteStringList($c->params['allowed']);
            $sql .= ' CHECK (' . $this->wrap($c->name) . ' IN (' . $list . '))';
        }
        return $sql;
    }

    public function createTableSuffix(): string { return ''; } // ENGINE/CHARSET yo'q

    public function compileIndexes(Blueprint $b): array
    {
        $after = [];
        $columns = $b->getColumns();
        foreach ($columns as $c) {
            if ($this->isIncrement($c)) continue;
            if ($c->unique) {
                $after[] = 'CREATE UNIQUE INDEX ' . $b->getTable() . '_' . $c->name . '_unique ON ' . $this->wrap($b->getTable()) . ' (' . $this->wrap($c->name) . ')';
            }
            if ($c->index) {
                $after[] = 'CREATE INDEX ' . $b->getTable() . '_' . $c->name . '_index ON ' . $this->wrap($b->getTable()) . ' (' . $this->wrap($c->name) . ')';
            }
        }
        return ['inline' => [], 'after' => $after];
    }

    public function alterAddColumnPrefix(): string { return 'ADD COLUMN'; }

    public function listTablesSql(): string
    {
        return "SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name";
    }

    public function hasTableSql(): string
    {
        return "SELECT name FROM sqlite_master WHERE type='table' AND name = ?";
    }

    public function listColumnsSql(string $table): string
    {
        return 'PRAGMA table_info(' . $this->wrap($table) . ')';
    }

    public function dropAllTables(array $tables): array
    {
        $stmts = ['PRAGMA foreign_keys = OFF'];
        foreach ($tables as $tbl) {
            $stmts[] = 'DROP TABLE IF EXISTS ' . $this->wrap($tbl);
        }
        $stmts[] = 'PRAGMA foreign_keys = ON';
        return $stmts;
    }
}