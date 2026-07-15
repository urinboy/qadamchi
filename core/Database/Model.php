<?php
namespace Qadamchi\Database;

use Qadamchi\Database\DB;
use Qadamchi\Database\QueryBuilder;
use Qadamchi\Exceptions\RouteNotFoundException;

/**
 * Eloquent'ga o'xshash Active Record model.
 * fillable / hidden / casts / timestamps / relations (hasOne, hasMany, belongsTo, belongsToMany)
 * accessors (getXAttribute) / mutators (setXAttribute) / find / findOrFail / create / paginate / toArray.
 */
abstract class Model
{
    protected ?string $table = null;
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $hidden = [];
    protected array $casts = [];
    public bool $timestamps = true;

    protected array $attributes = [];
    protected array $relations = [];
    public bool $exists = false;

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    /** Bazadan kelgan xom satr — yangi model, exists=true. */
    public function newFromBuilder(array $attributes = []): self
    {
        $instance = (new static);
        $instance->setRawAttributes((array) $attributes);
        $instance->exists = true;
        return $instance;
    }

    public function fill(array $attributes): self
    {
        foreach ($attributes as $key => $value) {
            if ($this->isFillable($key)) {
                $this->setAttribute($key, $value);
            }
        }
        return $this;
    }

    public function isFillable(string $key): bool
    {
        return in_array($key, $this->fillable, true) || empty($this->fillable);
    }

    public function setRawAttributes(array $attributes): self
    {
        $this->attributes = $attributes;
        return $this;
    }

    public function setAttribute(string $key, $value): self
    {
        $method = 'set' . str_replace(' ', '', ucwords(str_replace(['_', '-'], ' ', $key))) . 'Attribute';
        if (method_exists($this, $method)) {
            return $this->{$method}($value);
        }
        $this->attributes[$key] = $value;
        return $this;
    }

    public function getAttribute(string $key)
    {
        // 1) Accessor
        $method = 'get' . str_replace(' ', '', ucwords(str_replace(['_', '-'], ' ', $key))) . 'Attribute';
        if (method_exists($this, $method)) {
            return $this->{$method}();
        }
        // 2) Oddiy atribut (cast bilan)
        if (array_key_exists($key, $this->attributes)) {
            return $this->castAttribute($key, $this->attributes[$key]);
        }
        // 3) Relation (lazy load)
        if (method_exists($this, $key)) {
            return $this->getRelationValue($key);
        }
        return null;
    }

    public function hasAttribute(string $key): bool
    {
        return array_key_exists($key, $this->attributes);
    }

    public function getRelationValue(string $key)
    {
        if (array_key_exists($key, $this->relations)) {
            return $this->relations[$key];
        }
        if (method_exists($this, $key)) {
            $qb = $this->{$key}();
            if ($qb instanceof QueryBuilder) {
                return $this->relations[$key] = $qb->isSingle() ? $qb->first() : $qb->get();
            }
            return $this->relations[$key] = $qb;
        }
        return null;
    }

    public function setRelation(string $key, $value): self
    {
        $this->relations[$key] = $value;
        return $this;
    }

    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }

    public function __isset($key): bool
    {
        return $this->getAttribute($key) !== null;
    }

    // ---- Casts ----

    protected function castAttribute(string $key, $value)
    {
        if ($value === null) return null;
        $type = $this->casts[$key] ?? null;
        switch ($type) {
            case 'int': case 'integer': return (int) $value;
            case 'float': case 'double': return (float) $value;
            case 'bool': case 'boolean': return (bool) $value;
            case 'string': return (string) $value;
            case 'array': case 'json': return json_decode($value, true);
            case 'object': return json_decode($value);
            case 'date': return $value;
            default: return $value;
        }
    }

    // ---- Table / key ----

    public function getTable(): string
    {
        if ($this->table) return $this->table;
        $class = (new \ReflectionClass($this))->getShortName();
        return $this->pluralize(strtolower($class));
    }

    public function getKeyName(): string { return $this->primaryKey; }

    public function getKey()
    {
        return $this->attributes[$this->primaryKey] ?? null;
    }

    protected function pluralize(string $word): string
    {
        $irregular = [
            'user' => 'users', 'post' => 'posts', 'person' => 'people',
            'category' => 'categories', 'city' => 'cities', 'child' => 'children',
            'news' => 'news', 'series' => 'series',
        ];
        if (isset($irregular[$word])) return $irregular[$word];
        if (preg_match('/(s|x|z|ch|sh)$/i', $word)) return $word . 'es';
        if (preg_match('/[^aeiou]y$/i', $word)) return substr($word, 0, -1) . 'ies';
        return $word . 's';
    }

    // ---- Static CRUD ----

    public static function query(): QueryBuilder
    {
        return (new QueryBuilder())->setModel(new static());
    }

    public static function where($column, $operator = '=', $value = null): QueryBuilder
    {
        return static::query()->where($column, $operator, $value);
    }

    public static function all(): array
    {
        return static::query()->get();
    }

    public static function find($id): ?self
    {
        return static::query()->where((new static)->getKeyName(), $id)->first();
    }

    public static function findOrFail($id): self
    {
        $model = static::find($id);
        if ($model === null) {
            throw new \Qadamchi\Exceptions\RouteNotFoundException(get_called_class() . " #$id topilmadi");
        }
        return $model;
    }

    public static function create(array $attributes): self
    {
        $model = (new static)->fill($attributes);
        $model->save();
        return $model;
    }

    public static function firstOrCreate(array $where, array $extra = []): self
    {
        $model = static::query();
        foreach ($where as $k => $v) $model = $model->where($k, $v);
        $existing = $model->first();
        if ($existing) return $existing;
        return static::create(array_merge($where, $extra));
    }

    public static function updateOrCreate(array $where, array $values): self
    {
        $model = static::query();
        foreach ($where as $k => $v) $model = $model->where($k, $v);
        $existing = $model->first();
        if ($existing) {
            $existing->fill($values)->save();
            return $existing;
        }
        return static::create(array_merge($where, $values));
    }

    public static function destroy($ids): int
    {
        $ids = is_array($ids) ? $ids : func_get_args();
        $count = 0;
        foreach ($ids as $id) {
            if ($model = static::find($id)) {
                $model->delete();
                $count++;
            }
        }
        return $count;
    }

    // ---- Instance save/delete ----

    public function save(): self
    {
        if ($this->timestamps) {
            $now = date('Y-m-d H:i:s');
            if (!$this->exists) $this->setAttribute('created_at', $this->getAttribute('created_at') ?? $now);
            $this->setAttribute('updated_at', $now);
        }
        if ($this->exists) {
            $this->performUpdate();
        } else {
            $this->performInsert();
        }
        return $this;
    }

    protected function performInsert(): void
    {
        $data = $this->onlyFillable($this->attributes);
        $id = DB::table($this->getTable())->insert($data);
        $this->attributes[$this->primaryKey] = $id;
        $this->exists = true;
    }

    protected function performUpdate(): void
    {
        $data = $this->onlyFillable($this->attributes);
        unset($data[$this->primaryKey]);
        DB::table($this->getTable())->where($this->primaryKey, $this->getKey())->update($data);
    }

    public function delete(): void
    {
        if (!$this->exists) return;
        DB::table($this->getTable())->where($this->primaryKey, $this->getKey())->delete();
        $this->exists = false;
    }

    public function fresh(): ?self
    {
        if (!$this->exists) return null;
        return static::find($this->getKey());
    }

    protected function onlyFillable(array $data): array
    {
        if (empty($this->fillable)) return $data;
        return array_intersect_key($data, array_flip($this->fillable));
    }

    // ---- Relations ----

    public function hasOne(string $related, ?string $foreignKey = null, ?string $localKey = null): QueryBuilder
    {
        $foreignKey = $foreignKey ?: strtolower((new \ReflectionClass($this))->getShortName()) . '_id';
        $localKey = $localKey ?: $this->primaryKey;
        return $related::where($foreignKey, $this->getAttribute($localKey))->setSingle();
    }

    public function hasMany(string $related, ?string $foreignKey = null, ?string $localKey = null): QueryBuilder
    {
        $foreignKey = $foreignKey ?: strtolower((new \ReflectionClass($this))->getShortName()) . '_id';
        $localKey = $localKey ?: $this->primaryKey;
        return $related::where($foreignKey, $this->getAttribute($localKey));
    }

    public function belongsTo(string $related, ?string $foreignKey = null, ?string $ownerKey = null): QueryBuilder
    {
        $foreignKey = $foreignKey ?: strtolower((new \ReflectionClass($related))->getShortName()) . '_id';
        $ownerKey = $ownerKey ?: 'id';
        return $related::where($ownerKey, $this->getAttribute($foreignKey))->setSingle();
    }

    public function belongsToMany(string $related, ?string $table = null, ?string $foreignPivot = null, ?string $relatedPivot = null): QueryBuilder
    {
        $self = strtolower((new \ReflectionClass($this))->getShortName());
        $rel = strtolower((new \ReflectionClass($related))->getShortName());
        $table = $table ?: $this->pluralize($self) . '_' . $this->pluralize($rel);
        $foreignPivot = $foreignPivot ?: $self . '_id';
        $relatedPivot = $relatedPivot ?: $rel . '_id';

        $relatedTable = (new $related)->getTable();
        $qb = $related::query();
        $qb->join($table, "$relatedTable." . (new $related)->getKeyName(), '=', "$table.$relatedPivot")
           ->where("$table.$foreignPivot", $this->getKey());
        return $qb;
    }

    // ---- Serialization ----

    public function toArray(): array
    {
        $items = [];
        foreach ($this->attributes as $key => $value) {
            if (in_array($key, $this->hidden, true)) continue;
            $items[$key] = $this->getAttribute($key);
        }
        foreach ($this->relations as $key => $relation) {
            if (in_array($key, $this->hidden, true)) continue;
            $items[$key] = $this->relationToArray($relation);
        }
        return $items;
    }

    protected function relationToArray($relation)
    {
        if (is_array($relation)) {
            return array_map(fn($m) => $m instanceof self ? $m->toArray() : $relation, $relation);
        }
        return $relation instanceof self ? $relation->toArray() : $relation;
    }

    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }

    public function __toString(): string { return $this->toJson(); }
}