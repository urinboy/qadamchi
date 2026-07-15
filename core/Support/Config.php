<?php
namespace Qadamchi\Support;

/**
 * Konfiguratsiya repository (Laravel'ning config() g'oyasi).
 * config/app.php, config/db.php ... fayllarni lazy yuklaydi va keshlaydi.
 * Dot notation: config('db.host'), config('app.name').
 */
class Config
{
    protected string $configPath;
    protected array $cache = [];

    public function __construct(?string $configPath = null)
    {
        $this->configPath = $configPath ?? __DIR__ . '/../../config';
    }

    public function get(string $key, $default = null)
    {
        [$file, $path] = $this->splitKey($key);
        $items = $this->load($file);
        return $this->dataGet($items, $path, $default);
    }

    public function set(string $key, $value): void
    {
        [$file, $path] = $this->splitKey($key);
        $items = $this->load($file);
        $this->dataSet($items, $path, $value);
        $this->cache[$file] = $items;
    }

    public function all(): array
    {
        return $this->cache;
    }

    protected function splitKey(string $key): array
    {
        $segments = explode('.', $key);
        $file = array_shift($segments);
        return [$file, $segments];
    }

    protected function load(string $file): array
    {
        if (isset($this->cache[$file])) {
            return $this->cache[$file];
        }
        $path = $this->configPath . '/' . $file . '.php';
        $this->cache[$file] = is_file($path) ? (array) require $path : [];
        return $this->cache[$file];
    }

    protected function dataGet(array $items, array $path, $default)
    {
        foreach ($path as $segment) {
            if (!is_array($items) || !array_key_exists($segment, $items)) {
                return $default;
            }
            $items = $items[$segment];
        }
        return $items;
    }

    protected function dataSet(array &$items, array $path, $value): void
    {
        $ref = &$items;
        foreach ($path as $i => $segment) {
            if ($i === count($path) - 1) {
                $ref[$segment] = $value;
                return;
            }
            if (!isset($ref[$segment]) || !is_array($ref[$segment])) {
                $ref[$segment] = [];
            }
            $ref = &$ref[$segment];
        }
    }
}