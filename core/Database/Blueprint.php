<?php
namespace Qadamchi\Database;

/**
 * Schema blueprint — CREATE/ALTER TABLE uchun ustun ta'rifi.
 * Laravel'ning Blueprint g'oyasi, kam kod bilan.
 */
class Blueprint
{
    protected string $table;
    protected array $columns = [];
    protected array $commands = [];
    protected string $engine = 'InnoDB';
    protected string $charset = 'utf8mb4';

    public function __construct(string $table)
    {
        $this->table = $table;
    }

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

    // ---- SQL generatsiyasi ----

    protected function columnSql(ColumnDefinition $c): string
    {
        $name = $this->quote($c->name);
        switch ($c->type) {
            case 'bigIncrements': $sql = "$name BIGINT UNSIGNED AUTO_INCREMENT"; break;
            case 'increments':    $sql = "$name INT UNSIGNED AUTO_INCREMENT"; break;
            case 'string':        $sql = "$name VARCHAR({$c->params['length']})"; break;
            case 'text':          $sql = "$name TEXT"; break;
            case 'mediumText':    $sql = "$name MEDIUMTEXT"; break;
            case 'longText':      $sql = "$name LONGTEXT"; break;
            case 'integer':       $sql = $c->params['unsigned'] ? "$name INT UNSIGNED" : "$name INT"; break;
            case 'bigInteger':    $sql = $c->params['unsigned'] ? "$name BIGINT UNSIGNED" : "$name BIGINT"; break;
            case 'smallInteger':  $sql = "$name SMALLINT"; break;
            case 'boolean':       $sql = "$name TINYINT(1)"; break;
            case 'float':         $sql = "$name FLOAT({$c->params['precision']},{$c->params['scale']})"; break;
            case 'decimal':       $sql = "$name DECIMAL({$c->params['precision']},{$c->params['scale']})"; break;
            case 'enum':          $list = implode(',', array_map(fn($v) => "'" . addslashes($v) . "'", $c->params['allowed'])); $sql = "$name ENUM($list)"; break;
            case 'json':          $sql = "$name JSON"; break;
            case 'date':          $sql = "$name DATE"; break;
            case 'time':          $sql = "$name TIME"; break;
            case 'datetime':      $sql = "$name DATETIME"; break;
            case 'timestamp':     $sql = "$name TIMESTAMP"; break;
            case 'foreignId':     $sql = "$name BIGINT UNSIGNED"; break;
            default:              $sql = "$name VARCHAR(255)"; break;
        }

        if (in_array($c->type, ['bigIncrements', 'increments'], true)) {
            $sql .= ' PRIMARY KEY';
        }
        if ($c->nullable) $sql .= ' NULL'; else $sql .= ' NOT NULL';
        if ($c->default !== null) {
            $sql .= " DEFAULT '" . addslashes((string) $c->default) . "'";
        } elseif ($c->nullable && $c->type === 'timestamp') {
            $sql .= ' DEFAULT NULL';
        }
        if ($c->unique) $sql .= ' UNIQUE';
        if ($c->index) $sql .= ''; // index alohida
        if ($c->comment) $sql .= " COMMENT '" . addslashes($c->comment) . "'";

        return $sql;
    }

    public function toCreateSql(): string
    {
        $cols = array_map(fn($c) => $this->columnSql($c), $this->columns);
        $indexes = [];
        foreach ($this->columns as $c) {
            if ($c->unique && !in_array($c->type, ['bigIncrements', 'increments'], true)) {
                $indexes[] = "UNIQUE KEY {$this->table}_{$c->name}_unique ({$this->quote($c->name)})";
            }
            if ($c->index) {
                $indexes[] = "KEY {$this->table}_{$c->name}_index ({$this->quote($c->name)})";
            }
        }
        $all = implode(', ', array_filter(array_merge($cols, $indexes)));
        return "CREATE TABLE {$this->quoteIdent($this->table)} ($all) ENGINE={$this->engine} DEFAULT CHARSET={$this->charset}";
    }

    public function toAlterSql(): array
    {
        $sqls = [];
        foreach ($this->columns as $c) {
            $sqls[] = "ALTER TABLE {$this->quoteIdent($this->table)} ADD {$this->columnSql($c)}";
        }
        foreach ($this->commands as $cmd) {
            if ($cmd[0] === 'drop') {
                $sqls[] = "ALTER TABLE {$this->quoteIdent($this->table)} DROP COLUMN {$this->quote($cmd[1])}";
            }
        }
        return $sqls;
    }

    public function toDropSql(): string
    {
        return "DROP TABLE IF EXISTS {$this->quoteIdent($this->table)}";
    }

    protected function quote(string $name): string { return "`$name`"; }
    protected function quoteIdent(string $name): string { return "`$name`"; }
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