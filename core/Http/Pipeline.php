<?php
namespace Qadamchi\Http;

use Qadamchi\Container\Container;

/**
 * Middleware pipeline (onion modeli).
 * Pipeline::send($request)->through($middlewares)->then($final)
 */
class Pipeline
{
    protected $request;
    protected array $pipes = [];
    protected ?Container $container = null;

    public function __construct(?Container $container = null)
    {
        $this->container = $container;
    }

    public function send($request): self
    {
        $this->request = $request;
        return $this;
    }

    public function through(array $pipes): self
    {
        $this->pipes = $pipes;
        return $this;
    }

    public function then(\Closure $final)
    {
        $pipeline = array_reduce(
            array_reverse($this->pipes),
            $this->carry(),
            $final
        );
        return $pipeline($this->request);
    }

    protected function carry(): \Closure
    {
        return function (\Closure $next, $pipe) {
            return function ($request) use ($pipe, $next) {
                if (is_string($pipe)) {
                    $pipe = $this->container ? $this->container->make($pipe) : new $pipe();
                }
                if (is_callable($pipe)) {
                    return $pipe($request, $next);
                }
                return $pipe->handle($request, $next);
            };
        };
    }
}