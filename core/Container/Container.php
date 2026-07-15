<?php
namespace Qadamchi\Container;

use Qadamchi\Contracts\ContainerInterface;
use Qadamchi\Contracts\NotFoundException;
use Qadamchi\Contracts\ContainerException;
use ReflectionClass;
use ReflectionException;

/**
 * Mini service container (Laravel'ning Illuminate\Container g'oyasi, kam kod).
 * bind() / singleton() / resolve() (reflection autowire) / make() / get() / has().
 */
class Container implements ContainerInterface
{
    private array $bindings = [];
    private array $instances = [];
    private array $tags = [];

    protected static ?Container $instance = null;

    public static function getInstance(): self
    {
        return self::$instance;
    }

    public static function setInstance(self $instance): void
    {
        self::$instance = $instance;
    }

    public function bind(string $abstract, $concrete = null): void
    {
        $this->bindings[$abstract] = $concrete ?? $abstract;
    }

    public function singleton(string $abstract, $concrete = null): void
    {
        $this->bindings[$abstract] = $concrete ?? $abstract;
        $this->instances[$abstract] = null; // belgi: singleton
    }

    public function tag(string $abstract, string $tag): void
    {
        $this->tags[$tag][] = $abstract;
    }

    public function tagged(string $tag): array
    {
        $out = [];
        foreach ($this->tags[$tag] ?? [] as $abstract) {
            $out[] = $this->make($abstract);
        }
        return $out;
    }

    public function instance(string $abstract, $instance): void
    {
        $this->instances[$abstract] = $instance;
    }

    public function make(string $abstract, array $params = [])
    {
        return $this->resolve($abstract, $params);
    }

    public function get(string $id)
    {
        if (!$this->has($id)) {
            throw new NotFoundException("Binding topilmadi: $id");
        }
        return $this->make($id);
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->bindings)
            || array_key_exists($id, $this->instances)
            || class_exists($id);
    }

    public function resolve(string $abstract, array $params = [])
    {
        // Singleton: allaqachon yaratilgan bo'lsa qaytaramiz
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        $concrete = $this->bindings[$abstract] ?? $abstract;

        // Closure binding
        if ($concrete instanceof \Closure) {
            $object = $concrete($this, $params);
        } else {
            $object = $this->build($concrete, $params);
        }

        // Singleton belgisi bo'lsa — saqlab qo'yamiz
        if (array_key_exists($abstract, $this->instances)) {
            $this->instances[$abstract] = $object;
        }

        return $object;
    }

    protected function build($concrete, array $params = [])
    {
        if (!is_string($concrete) || !class_exists($concrete)) {
            throw new ContainerException("Sinfni yaratib bo'lmadi: " . (is_string($concrete) ? $concrete : gettype($concrete)));
        }

        try {
            $reflector = new ReflectionClass($concrete);
        } catch (ReflectionException $e) {
            throw new ContainerException("Reflection xatosi: $concrete — " . $e->getMessage());
        }

        if (!$reflector->isInstantiable()) {
            throw new ContainerException("Sinfni instantiate qilib bo'lmadi: $concrete");
        }

        $constructor = $reflector->getConstructor();
        if ($constructor === null) {
            return new $concrete();
        }

        $deps = [];
        foreach ($constructor->getParameters() as $param) {
            $type = $param->getType();
            $typeName = $type && !$type->isBuiltin() ? $type->getName() : null;

            if (isset($params[$param->getName()])) {
                $deps[] = $params[$param->getName()];
            } elseif ($typeName && $this->has($typeName)) {
                $deps[] = $this->make($typeName);
            } elseif ($typeName && class_exists($typeName)) {
                $deps[] = $this->make($typeName);
            } elseif ($param->isDefaultValueAvailable()) {
                $deps[] = $param->getDefaultValue();
            } elseif ($param->allowsNull()) {
                $deps[] = null;
            } else {
                throw new ContainerException("Dependency yechilmadi: \$$param->name ($concrete)");
            }
        }

        return $reflector->newInstanceArgs($deps);
    }

    public function call($callback, array $params = [])
    {
        if (is_array($callback)) {
            [$class, $method] = $callback;
            $instance = is_object($class) ? $class : $this->make($class);
            return $this->callMethod($instance, $method, $params);
        }
        if (is_string($callback) && strpos($callback, '@')) {
            [$class, $method] = explode('@', $callback, 2);
            $instance = $this->make($class);
            return $this->callMethod($instance, $method, $params);
        }
        // Closure / function — reflection bilan dependency injection
        $ref = new \ReflectionFunction($callback);
        $args = $this->resolveParams($ref->getParameters(), $params);
        return $callback(...$args);
    }

    protected function callMethod($instance, string $method, array $params = [])
    {
        $ref = new \ReflectionMethod($instance, $method);
        $args = $this->resolveParams($ref->getParameters(), $params);
        return $ref->invokeArgs($instance, $args);
    }

    protected function resolveParams(array $reflectionParams, array $given = []): array
    {
        $args = [];
        foreach ($reflectionParams as $param) {
            $type = $param->getType();
            $typeName = $type && !$type->isBuiltin() ? $type->getName() : null;

            if (array_key_exists($param->getName(), $given)) {
                $args[] = $given[$param->getName()];
            } elseif ($typeName && ($this->has($typeName) || class_exists($typeName))) {
                $args[] = $this->make($typeName);
            } elseif ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();
            } elseif ($param->allowsNull()) {
                $args[] = null;
            } else {
                $args[] = null;
            }
        }
        return $args;
    }

    public function flush(): void
    {
        $this->bindings = [];
        $this->instances = [];
        $this->tags = [];
    }
}