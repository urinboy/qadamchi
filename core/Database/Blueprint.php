<?php
namespace Qadamchi\Database;

use Qadamchi\Database\Grammars\SchemaGrammar;

/**
 * Schema blueprint — CREATE/ALTER TABLE uchun ustun ta'rifi.
 * Laravel'ning Blueprint g'oyasi, driver grammar'lar bilan ishlaydi.
 *
 * SQL generatsiyasi (ustun turlari, quote, indexlar, ENGINE/CHARSET) grammar'ga
 * topshiriladi — migration kodi driver-agnostik qoladi.
 */
class Blueprint
{
    protected string $table;
    protected array $columns = [];
    protected array $commands = [];
    protected SchemaGrammar $grammar;

    public function __construct(string $table, ?SchemaGrammar $grammar = null)
    {
        $this->table = $table;
        // Grammar berilmagan bo'lsa, joriy driver bo'yicha default grammar.
        $this->grammar = $grammar ?? SchemaGrammar::forDriver(DB::driver());
    }

    /** Grammar — Schema va grammar'lar ushbu orqali ustun/jadval ma'lumotini o'qiydi. */
    public function getGrammar(): SchemaGrammar { return $this->grammar; }
    public function getTable(): string { return $this->table; }
    public function getColumns(): array { return $this->columns; }

    public function id(string $column = 'id'): ColumnDefinition
    {
        return $this->bigIncrements($column);
    }

    public function bigIncrements(string $column): ColumnDefinition
    {
        return $this->addColumn('bigIncrements', $column);
    }

    public function increments(string $column): ColumnDefinition
    {
        return $this->addColumn('increments', $column);
    }

    public function string(string $column, int $length = 255): ColumnDefinition
    {
        return $this->addColumn('string', $column, ['length' => $length]);
    }

    public function text(string $column): ColumnDefinition { return $this->addColumn('text', $column); }
    public function mediumText(string $column): ColumnDefinition { return $this->addColumn('mediumText', $column); }
    public function longText(string $column): ColumnDefinition { return $this->addColumn('longText', $column); }

    public function integer(string $column, bool $unsigned = false): ColumnDefinition
    {
        return $this->addColumn('integer', $column, ['unsigned' => $unsigned]);
    }

    public function bigInteger(string $column, bool $unsigned = false): ColumnDefinition
    {
        return $this->addColumn('bigInteger', $column, ['unsigned' => $unsigned]);
    }

    public function smallInteger(string $column): ColumnDefinition { return $this->addColumn('smallInteger', $column); }
    public function boolean(string $column): ColumnDefinition { return $this->addColumn('boolean', $column); }

    public function float(string $column, int $precision = 8, int $scale = 2): ColumnDefinition
    {
        return $this->addColumn('float', $column, ['precision' => $precision, 'scale' => $scale]);
    }

    public function decimal(string $column, int $precision = 8, int $scale = 2): ColumnDefinition
    {
        return $this->addColumn('decimal', $column, ['precision' => $precision, 'scale' => $scale]);
    }

    public function enum(string $column, array $allowed): ColumnDefinition
    {
        return $this->addColumn('enum', $column, ['allowed' => $allowed]);
    }

    public function json(string $column): ColumnDefinition { return $this->addColumn('json', $column); }

    public function date(string $column): ColumnDefinition { return $this->addColumn('date', $column); }
    public function time(string $column): ColumnDefinition { return $this->addColumn('time', $column); }
    public function datetime(string $column): ColumnDefinition { return $this->addColumn('datetime', $column); }
    public function timestamp(string $column): ColumnDefinition { return $this->addColumn('timestamp', $column); }

    public function timestamps(): void
    {
        $this->addColumn('timestamp', 'created_at')->nullable();
        $this->addColumn('timestamp', 'updated_at')->nullable();
    }

    public function foreignId(string $column): ColumnDefinition
    {
        return $this->addColumn('foreignId', $column)->unsigned();
    }

    public function addColumn(string $type, string $name, array $params = []): ColumnDefinition
    {
        $def = new ColumnDefinition($type, $name, $params);
        $this->columns[] = $def;
        return $def;
    }

    public function dropColumn(string $column): void { $this->commands[] = ['drop', $column]; }

    // ---- SQL generatsiyasi (grammar orqali) ----

    protected function columnSql(ColumnDefinition $c): string
    {
        return $this->grammar->compileColumn($c);
    }

    /**
     * CREATE TABLE uchun barcha statementlar (array).
     * SQLite'da indexlar alohida CREATE INDEX statementlari bo'lishi mumkin.
     */
    public function toCreateStatements(): array
    {
        $cols = array_map(fn($c) => $this->columnSql($c), $this->columns);
        $indexes = $this->grammar->compileIndexes($this);
        $inline = $indexes['inline'];
        $after = $indexes['after'];

        $body = implode(', ', array_filter(array_merge($cols, $inline)));
        $suffix = $this->grammar->createTableSuffix();
        $createSql = 'CREATE TABLE ' . $this->grammar->wrap($this->table) . ' (' . $body . ')';
        if ($suffix !== '') $createSql .= ' ' . $suffix;

        return array_merge([$createSql], $after);
    }

    /** Eskidek bitta string kerak bo'lsa (kamdan-kam). */
    public function toCreateSql(): string
    {
        $stmts = $this->toCreateStatements();
        return $stmts[0];
    }

    public function toAlterSql(): array
    {
        $prefix = $this->grammar->alterAddColumnPrefix();
        $sqls = [];
        foreach ($this->columns as $c) {
            $sqls[] = 'ALTER TABLE ' . $this->grammar->wrap($this->table) . ' ' . $prefix . ' ' . $this->columnSql($c);
        }
        foreach ($this->commands as $cmd) {
            if ($cmd[0] === 'drop') {
                $sqls[] = 'ALTER TABLE ' . $this->grammar->wrap($this->table) . ' DROP COLUMN ' . $this->grammar->wrap($cmd[1]);
            }
        }
        return $sqls;
    }

    public function toDropSql(): string
    {
        return 'DROP TABLE IF EXISTS ' . $this->grammar->wrap($this->table);
    }

    public function toDropNoIfSql(): string
    {
        return 'DROP TABLE ' . $this->grammar->wrap($this->table);
    }
}

class ColumnDefinition
{
    public string $type;
    public string $name;
    public array $params;
    public bool $nullable = false;
    public $default = null;
    public bool $unique = false;
    public bool $index = false;
    public ?string $comment = null;

    public function __construct(string $type, string $name, array $params = [])
    {
        $this->type = $type;
        $this->name = $name;
        $this->params = $params + ['length' => 255, 'unsigned' => false, 'precision' => 8, 'scale' => 2, 'allowed' => []];
    }

    public function nullable(): self { $this->nullable = true; return $this; }
    public function default($value): self { $this->default = $value; return $this; }
    public function unique(): self { $this->unique = true; return $this; }
    public function index(): self { $this->index = true; return $this; }
    public function comment(string $comment): self { $this->comment = $comment; return $this; }
    public function unsigned(): self { $this->params['unsigned'] = true; return $this; }
    public function references(string $table, string $column = 'id'): self
    {
        $this->params['references'] = "$table($column)";
        return $this;
    }
}