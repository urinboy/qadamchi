<?php
namespace Qadamchi\Database;

use Qadamchi\Database\DB;
use Qadamchi\Database\Model;

/**
 * Fluent query builder (Laravel'ning QueryBuilder g'oyasi).
 * where/orWhere/whereIn/whereNull/select/distinct/join/orderBy/limit/offset/groupBy/having/get/first/count/paginate.
 * Qiymatlar parametrlangan (SQL injection'dan himoya); limit/offset int, direction whitelist.
 */
class QueryBuilder
{
    protected ?Model $model = null;
    protected ?string $table = null;
    protected bool $isSingle = false;
    protected array $wheres = [];
    protected array $orders = [];
    protected ?int $limit = null;
    protected ?int $offset = null;
    protected array $selects = ['*'];
    protected bool $distinct = false;
    protected array $joins = [];
    protected array $groups = [];
    protected array $havings = [];
    protected array $bindings = [];

    public function __construct(?Model $model = null) { $this->model = $model; }

    public function from(string $table): self { $this->table = $table; return $this; }

    public function setModel(Model $model): self { $this->model = $model; return $this; }

    public function select($columns = ['*']): self
    {
        $this->selects = is_array($columns) ? $columns : func_get_args();
        return $this;
    }

    public function distinct(): self { $this->distinct = true; return $this; }

    /** Relation uchun: hasOne/belongsTo (yagona) yoki hasMany (ko'p). */
    public function setSingle(bool $single = true): self { $this->isSingle = $single; return $this; }
    public function isSingle(): bool { return $this->isSingle; }

    public function where($column, $operator = '=', $value = null, string $boolean = 'AND'): self
    {
        if ($value === null && $operator !== '=' && $operator !== 'is') {
            $value = $operator;
            $operator = '=';
        }
        if (is_array($value)) {
            return $this->whereIn($column, $value, $boolean);
        }
        $this->wheres[] = compact('column', 'operator', 'value', 'boolean');
        return $this;
    }

    public function orWhere($column, $operator = '=', $value = null): self
    {
        return $this->where($column, $operator, $value, 'OR');
    }

    public function whereIn(string $column, array $values, string $boolean = 'AND'): self
    {
        if (empty($values)) {
            $this->wheres[] = ['column' => '1', 'operator' => '=', 'value' => 0, 'boolean' => $boolean, 'type' => 'raw'];
            return $this;
        }
        $this->wheres[] = ['column' => $column, 'operator' => 'IN', 'value' => $values, 'boolean' => $boolean, 'type' => 'in'];
        return $this;
    }

    public function whereNull(string $column, string $boolean = 'AND'): self
    {
        $this->wheres[] = ['column' => $column, 'operator' => 'IS NULL', 'value' => null, 'boolean' => $boolean, 'type' => 'null'];
        return $this;
    }

    public function whereNotNull(string $column, string $boolean = 'AND'): self
    {
        $this->wheres[] = ['column' => $column, 'operator' => 'IS NOT NULL', 'value' => null, 'boolean' => $boolean, 'type' => 'null'];
        return $this;
    }

    public function join(string $table, $first, $operator, $second = null, string $type = 'INNER'): self
    {
        $this->joins[] = compact('table', 'first', 'operator', 'second', 'type');
        return $this;
    }

    public function leftJoin(string $table, $first, $operator, $second = null): self
    {
        return $this->join($table, $first, $operator, $second, 'LEFT');
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
        $this->orders[] = [$column, $direction];
        return $this;
    }

    public function orderByDesc(string $column): self { return $this->orderBy($column, 'DESC'); }

    public function limit(int $limit): self { $this->limit = max(0, (int) $limit); return $this; }
    public function take(int $limit): self { return $this->limit($limit); }

    public function offset(int $offset): self { $this->offset = max(0, (int) $offset); return $this; }
    public function skip(int $offset): self { return $this->offset($offset); }

    public function groupBy($columns): self
    {
        $this->groups = array_merge($this->groups, is_array($columns) ? $columns : func_get_args());
        return $this;
    }

    public function having(string $column, string $operator, $value): self
    {
        $this->havings[] = compact('column', 'operator', 'value');
        return $this;
    }

    protected function table(): string
    {
        return $this->table ?? $this->model->getTable();
    }

    protected function buildSelectSql(): array
    {
        $sql = ($this->distinct ? 'SELECT DISTINCT ' : 'SELECT ') . implode(', ', $this->selects) . ' FROM ' . $this->table();
        $bindings = [];

        foreach ($this->joins as $join) {
            $sql .= ' ' . $join['type'] . ' JOIN ' . $join['table'] . ' ON ' . $join['first'] . ' ' . $join['operator'] . ' ' . $join['second'];
        }

        if ($this->wheres) {
            $clauses = [];
            foreach ($this->wheres as $i => $w) {
                $boolean = $i === 0 ? '' : ' ' . $w['boolean'] . ' ';
                $col = $w['column'];
                if (($w['type'] ?? null) === 'in') {
                    $placeholders = implode(', ', array_fill(0, count($w['value']), '?'));
                    $clauses[] = $boolean . "$col IN ($placeholders)";
                    foreach ($w['value'] as $v) $bindings[] = $v;
                } elseif (($w['type'] ?? null) === 'null') {
                    $clauses[] = $boolean . "$col {$w['operator']}";
                } elseif (($w['type'] ?? null) === 'raw') {
                    $clauses[] = $boolean . "$col {$w['operator']} ?";
                    $bindings[] = $w['value'];
                } else {
                    $clauses[] = $boolean . "$col {$w['operator']} ?";
                    $bindings[] = $w['value'];
                }
            }
            $sql .= ' WHERE ' . implode('', $clauses);
        }

        if ($this->groups) {
            $sql .= ' GROUP BY ' . implode(', ', $this->groups);
        }
        if ($this->havings) {
            $clauses = [];
            foreach ($this->havings as $h) {
                $clauses[] = "$h[column] $h[operator] ?";
                $bindings[] = $h['value'];
            }
            $sql .= ' HAVING ' . implode(' AND ', $clauses);
        }
        if ($this->orders) {
            $clauses = array_map(fn($o) => "$o[0] $o[1]", $this->orders);
            $sql .= ' ORDER BY ' . implode(', ', $clauses);
        }
        if ($this->limit !== null) {
            $sql .= ' LIMIT ' . $this->limit;
        }
        if ($this->offset !== null) {
            $sql .= ' OFFSET ' . $this->offset;
        }

        return [$sql, $bindings];
    }

    public function get(): array
    {
        [$sql, $bindings] = $this->buildSelectSql();
        $rows = DB::query($sql, $bindings)->fetchAll();
        return $this->model ? $this->hydrate($rows) : $rows;
    }

    public function first(): ?Model
    {
        $this->limit(1);
        $rows = $this->get();
        return $rows[0] ?? null;
    }

    public function find($id): ?Model
    {
        return $this->where($this->model->getKeyName(), $id)->first();
    }

    public function count(): int
    {
        $orig = $this->selects;
        $this->selects = ['COUNT(*) AS aggregate'];
        [$sql, $bindings] = $this->buildSelectSql();
        $this->selects = $orig;
        return (int) DB::query($sql, $bindings)->fetchColumn();
    }

    public function exists(): bool { return $this->count() > 0; }

    public function paginate(int $perPage = 15, ?int $page = null): array
    {
        $page = $page ?? (int) ($_GET['page'] ?? 1);
        $page = max(1, $page);
        $total = (clone $this)->count();
        $this->limit($perPage)->offset(($page - 1) * $perPage);
        $items = $this->get();

        return [
            'data' => $items,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => (int) ceil($total / max(1, $perPage)),
            'has_more' => $page < (int) ceil($total / max(1, $perPage)),
        ];
    }

    public function insert(array $data): int
    {
        $columns = array_keys($data);
        $placeholders = implode(', ', array_fill(0, count($columns), '?'));
        $sql = 'INSERT INTO ' . $this->table() . ' (' . implode(', ', $columns) . ') VALUES (' . $placeholders . ')';
        DB::query($sql, array_values($data));
        return (int) DB::connection()->lastInsertId();
    }

    public function update(array $data): int
    {
        $set = implode(', ', array_map(fn($c) => "$c = ?", array_keys($data)));
        $bindings = array_values($data);
        $sql = 'UPDATE ' . $this->table() . ' SET ' . $set;
        [$whereSql, $whereBindings] = $this->buildWhereOnly();
        if ($whereSql) {
            $sql .= ' WHERE ' . $whereSql;
            $bindings = array_merge($bindings, $whereBindings);
        }
        return DB::query($sql, $bindings)->rowCount();
    }

    public function delete(): int
    {
        [$whereSql, $bindings] = $this->buildWhereOnly();
        $sql = 'DELETE FROM ' . $this->table();
        if ($whereSql) {
            $sql .= ' WHERE ' . $whereSql;
        }
        return DB::query($sql, $bindings)->rowCount();
    }

    protected function buildWhereOnly(): array
    {
        if (!$this->wheres) return ['', []];
        $clauses = []; $bindings = [];
        foreach ($this->wheres as $i => $w) {
            $boolean = $i === 0 ? '' : ' ' . $w['boolean'] . ' ';
            $col = $w['column'];
            if (($w['type'] ?? null) === 'in') {
                $placeholders = implode(', ', array_fill(0, count($w['value']), '?'));
                $clauses[] = $boolean . "$col IN ($placeholders)";
                foreach ($w['value'] as $v) $bindings[] = $v;
            } elseif (($w['type'] ?? null) === 'null') {
                $clauses[] = $boolean . "$col {$w['operator']}";
            } else {
                $clauses[] = $boolean . "$col {$w['operator']} ?";
                $bindings[] = $w['value'];
            }
        }
        return [implode('', $clauses), $bindings];
    }

    protected function hydrate(array $rows): array
    {
        $class = get_class($this->model);
        return array_map(fn($row) => (new $class)->newFromBuilder($row), $rows);
    }
}