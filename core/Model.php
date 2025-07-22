<?php

// core/Model.php - to'liq qayta yozish
abstract class Model {
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $attributes = [];
    
    public function __construct($attributes = []) {
        $this->attributes = $attributes;
        if (!$this->table) {
            $this->table = strtolower(get_class($this)) . 's';
        }
    }
    
    // Static methods
    public static function find($id) {
        $instance = new static;
        $sql = "SELECT * FROM {$instance->table} WHERE {$instance->primaryKey} = ?";
        $result = DB::query($sql, [$id])->fetch();
        return $result ? new static($result) : null;
    }
    
    public static function where($column, $operator = '=', $value = null) {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        return new QueryBuilder(new static, $column, $operator, $value);
    }
    
    public static function all() {
        $instance = new static;
        $sql = "SELECT * FROM {$instance->table}";
        $results = DB::query($sql)->fetchAll();
        return array_map(fn($row) => new static($row), $results);
    }
    
    public static function create($data) {
        $instance = new static($data);
        $instance->save();
        return $instance;
    }
    
    // Instance methods
    public function save() {
        if (isset($this->attributes[$this->primaryKey])) {
            return $this->update();
        } else {
            return $this->insert();
        }
    }
    
    private function insert() {
        $fillableData = array_intersect_key($this->attributes, array_flip($this->fillable));
        $columns = implode(',', array_keys($fillableData));
        $placeholders = ':' . implode(', :', array_keys($fillableData));
        
        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        DB::query($sql, $fillableData);
        
        $this->attributes[$this->primaryKey] = DB::connection()->lastInsertId();
        return $this;
    }
    
    private function update() {
        $fillableData = array_intersect_key($this->attributes, array_flip($this->fillable));
        $setParts = array_map(fn($col) => "$col = :$col", array_keys($fillableData));
        $setClause = implode(', ', $setParts);
        
        $sql = "UPDATE {$this->table} SET $setClause WHERE {$this->primaryKey} = :{$this->primaryKey}";
        $fillableData[$this->primaryKey] = $this->attributes[$this->primaryKey];
        
        DB::query($sql, $fillableData);
        return $this;
    }
    
    public function delete() {
        if (!isset($this->attributes[$this->primaryKey])) {
            return false;
        }
        
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        DB::query($sql, [$this->attributes[$this->primaryKey]]);
        return true;
    }
    
    // Magic methods
    public function __get($key) {
        return $this->attributes[$key] ?? null;
    }
    
    public function __set($key, $value) {
        $this->attributes[$key] = $value;
    }
}

// abstract class Model {
//     // Model uchun asos (bazaviy PDO logikasi)
// }