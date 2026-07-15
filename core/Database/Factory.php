<?php
namespace Qadamchi\Database;

/**
 * Factory bazasi — test/seed ma'lumotlarni generatsiya qilish (Laravel uslubi).
 *   User::factory()->create(['email' => 'x@y.uz'])  -> bazaga saqlaydi
 *   User::factory()->make()                         -> model instance (saqlanmagan)
 *   User::factory()->count(5)                       -> 5 ta make() instance
 *
 * Konventsiya: `Database\Factories\<Model>Factory` (PSR-4 autoload).
 */
abstract class Factory
{
    /** To'liq model FQCN. */
    protected string $model;

    /** Yangi factory instance (Laravel factory() bilan mos). */
    public static function new(): static
    {
        return new static();
    }

    /** Standart qiymatlar — bolalar klassida implement qilinadi. */
    abstract public function definition(): array;

    /** Model instance yasaydi (bazaga saqlamaydi). */
    public function make(array $overrides = [])
    {
        $model = $this->model;
        return new $model(array_merge($this->definition(), $overrides));
    }

    /** Model instance yasab, bazaga saqlaydi. */
    public function create(array $overrides = [])
    {
        $model = $this->model;
        return $model::create(array_merge($this->definition(), $overrides));
    }

    /** N ta make() instance qaytaradi (bazaga saqlamaydi). */
    public function count(int $n): array
    {
        return array_map(fn() => static::new()->make(), range(1, max(1, $n)));
    }
}