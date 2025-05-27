<?php

class Schema
{
    protected $table;
    protected $columns = [];
    protected $primary = null;
    protected $uniques = [];
    protected $indexes = [];
    protected $engine = 'InnoDB';

    public static function create($table, $callback)
    {
        $schema = new static;
        $schema->table = $table;
        $callback($schema); // $table->bigIncrements('id'); va h.k.
        $sql = $schema->getCreateTableSQL();
        $schema->run($sql);
    }

    public static function drop($table)
    {
        $sql = "DROP TABLE IF EXISTS `$table`";
        static::run($sql);
    }

    public static function table($table, $callback)
    {
        // extension: column qo‘shish/o‘chirish uchun
    }

    public function id($name = 'id')
    {
        $this->columns[] = "`$name` BIGINT UNSIGNED AUTO_INCREMENT";
        $this->primary = $name;
        return $this;
    }

    public function bigIncrements($name)
    {
        return $this->id($name);
    }

    public function string($name, $len = 255)
    {
        $this->columns[] = "`$name` VARCHAR($len)";
        return $this;
    }

    public function integer($name, $unsigned = false)
    {
        $this->columns[] = "`$name` INT" . ($unsigned ? " UNSIGNED" : "");
        return $this;
    }

    public function boolean($name)
    {
        $this->columns[] = "`$name` TINYINT(1)";
        return $this;
    }

    public function timestamps()
    {
        $this->columns[] = "`created_at` TIMESTAMP NULL DEFAULT NULL";
        $this->columns[] = "`updated_at` TIMESTAMP NULL DEFAULT NULL";
        return $this;
    }

    public function unique($col)
    {
        $this->uniques[] = $col;
        return $this;
    }

    public function index($col)
    {
        $this->indexes[] = $col;
        return $this;
    }

    protected function getCreateTableSQL()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `{$this->table}` (\n";
        $cols = $this->columns;
        if ($this->primary) $cols[] = "PRIMARY KEY (`{$this->primary}`)";
        foreach ($this->uniques as $u) $cols[] = "UNIQUE (`$u`)";
        foreach ($this->indexes as $i) $cols[] = "INDEX (`$i`)";
        $sql .= "  " . implode(",\n  ", $cols) . "\n";
        $sql .= ") ENGINE={$this->engine} DEFAULT CHARSET=utf8mb4;";
        return $sql;
    }

    protected static function run($sql)
    {
        $db = require __DIR__.'/../config/db.php';
        $pdo = new PDO("mysql:host={$db['host']};dbname={$db['name']};charset=utf8", $db['user'], $db['pass']);
        $pdo->exec($sql);
    }
}