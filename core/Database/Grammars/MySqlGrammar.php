<?php
namespace Qadamchi\Database\Grammars;

use Qadamchi\Database\Blueprint;
use Qadamchi\Database\ColumnDefinition;

/**
 * MySQL grammar — eskidek ishlovchi MySQL DDL.
 *
 * Backtick quote, UNSIGNED, AUTO_INCREMENT, TINYINT(1), ENUM,
 * MEDIUMTEXT/LONGTEXT, ENGINE=InnoDB DEFAULT CHARSET, UNIQUE KEY/KEY (inline),
 * SHOW TABLES / SHOW COLUMNS, SET FOREIGN_KEY_CHECKS.
 */
class MySqlGrammar extends SchemaGrammar
{
    protected string $engine = 'InnoDB';
    protected string $charset = 'utf8mb4';

    public function wrap(string $name): string
    {
        return '`' . str_replace('`', '``', $name) . '`';
    }

    protected function typeSql(ColumnDefinition $c): string
    {
        switch ($c->type) {
            case 'bigIncrements': return 'BIGINT UNSIGNED AUTO_INCREMENT';
            case 'increments':    return 'INT UNSIGNED AUTO_INCREMENT';
            case 'string':        return 'VARCHAR(' . (int) $c->params['length'] . ')';
            case 'text':          return 'TEXT';
            case 'mediumText':    return 'MEDIUMTEXT';
            case 'longText':      return 'LONGTEXT';
            case 'integer':       return ($c->params['unsigned'] ? 'INT UNSIGNED' : 'INT');
            case 'bigInteger':    return ($c->params['unsigned'] ? 'BIGINT UNSIGNED' : 'BIGINT');
            case 'smallInteger':  return 'SMALLINT';
            case 'boolean':       return 'TINYINT(1)';
            case 'float':         return 'FLOAT(' . (int) $c->params['precision'] . ',' . (int) $c->params['scale'] . ')';
            case 'decimal':       return 'DECIMAL(' . (int) $c->params['precision'] . ',' . (int) $c->params['scale'] . ')';
            case 'enum':          return 'ENUM(' . $this->quoteStringList($c->params['allowed']) . ')';
            case 'json':          return 'JSON';
            case 'date':          return 'DATE';
            case 'time':          return 'TIME';
            case 'datetime':      return 'DATETIME';
            case 'timestamp':     return 'TIMESTAMP';
            case 'foreignId':     return 'BIGINT UNSIGNED';
            default:              return 'VARCHAR(255)';
        }
    }

    protected function primaryKeyAppended(): bool { return true; }

    public function createTableSuffix(): string
    {
        return 'ENGINE=' . $this->engine . ' DEFAULT CHARSET=' . $this->charset;
    }

    public function compileIndexes(Blueprint $b): array
    {
        $inline = [];
        $columns = $b->getColumns();
        foreach ($columns as $c) {
            if ($this->isIncrement($c)) continue;
            if ($c->unique) {
                $inline[] = 'UNIQUE KEY ' . $b->getTable() . '_' . $c->name . '_unique (' . $this->wrap($c->name) . ')';
            }
            if ($c->index) {
                $inline[] = 'KEY ' . $b->getTable() . '_' . $c->name . '_index (' . $this->wrap($c->name) . ')';
            }
        }
        return ['inline' => $inline, 'after' => []];
    }

    public function alterAddColumnPrefix(): string { return 'ADD'; }

    public function listTablesSql(): string
    {
        return 'SHOW TABLES';
    }

    /**
     * information_schema orqali — SHOW TABLES LIKE ? MariaDB'da
     * placeholder'ni qo'llab-quvvatlamaydi (metadata statement), shu sababli
     * oddiy SELECT ishlatamiz (placeholder ishlaydi).
     */
    public function hasTableSql(): string
    {
        return 'SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ? LIMIT 1';
    }

    public function listColumnsSql(string $table): string
    {
        return 'SHOW COLUMNS FROM ' . $this->wrap($table);
    }

    public function dropAllTables(array $tables): array
    {
        $stmts = ['SET FOREIGN_KEY_CHECKS = 0'];
        foreach ($tables as $tbl) {
            $stmts[] = 'DROP TABLE IF EXISTS ' . $this->wrap($tbl);
        }
        $stmts[] = 'SET FOREIGN_KEY_CHECKS = 1';
        return $stmts;
    }

    protected function supportsColumnComment(): bool { return true; }
}