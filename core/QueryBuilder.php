<?php

// core/QueryBuilder.php - yangi fayl
class QueryBuilder {
    private $model;
    private $wheres = [];
    private $orders = [];
    private $limit;
    private $offset;
    
    public function __construct($model, $column = null, $operator = null, $value = null) {
        $this->model = $model;
        if ($column) {
            $this->wheres[] = [$column, $operator, $value];
        }
    }
    
    public function where($column, $operator = '=', $value = null) {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        $this->wheres[] = [$column, $operator, $value];
        return $this;
    }
    
    public function orderBy($column, $direction = 'ASC') {
        $this->orders[] = [$column, $direction];
        return $this;
    }
    
    public function limit($limit) {
        $this->limit = $limit;
        return $this;
    }
    
    public function offset($offset) {
        $this->offset = $offset;
        return $this;
    }
    
    public function get() {
        $sql = "SELECT * FROM {$this->model->table}";
        $params = [];
        
        if ($this->wheres) {
            $whereClauses = [];
            foreach ($this->wheres as $where) {
                $whereClauses[] = "{$where[0]} {$where[1]} ?";
                $params[] = $where[2];
            }
            $sql .= " WHERE " . implode(' AND ', $whereClauses);
        }
        
        if ($this->orders) {
            $orderClauses = array_map(fn($order) => "{$order[0]} {$order[1]}", $this->orders);
            $sql .= " ORDER BY " . implode(', ', $orderClauses);
        }
        
        if ($this->limit) {
            $sql .= " LIMIT {$this->limit}";
        }
        
        if ($this->offset) {
            $sql .= " OFFSET {$this->offset}";
        }
        
        $results = DB::query($sql, $params)->fetchAll();
        return array_map(fn($row) => new (get_class($this->model))($row), $results);
    }
    
    public function first() {
        $this->limit(1);
        $results = $this->get();
        return $results[0] ?? null;
    }
    
    public function count() {
        $sql = "SELECT COUNT(*) FROM {$this->model->table}";
        $params = [];
        
        if ($this->wheres) {
            $whereClauses = [];
            foreach ($this->wheres as $where) {
                $whereClauses[] = "{$where[0]} {$where[1]} ?";
                $params[] = $where[2];
            }
            $sql .= " WHERE " . implode(' AND ', $whereClauses);
        }
        
        return (int) DB::query($sql, $params)->fetchColumn();
    }
}