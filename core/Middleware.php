<?php
/**
 * Base Middleware class for Qadamchi Framework
 */
abstract class Middleware {
    /**
     * Handle an incoming request
     *
     * @param mixed $request
     * @param \Closure $next
     * @return mixed
     */
    abstract public function handle($request, $next);
    
    /**
     * Execute middleware pipeline
     */
    public static function run($middlewares, $request, $finalCallback) {
        $pipeline = array_reduce(
            array_reverse($middlewares),
            function ($next, $middleware) {
                return function ($request) use ($middleware, $next) {
                    if (is_string($middleware)) {
                        $middleware = new $middleware;
                    }
                    return $middleware->handle($request, $next);
                };
            },
            $finalCallback
        );
        
        return $pipeline($request);
    }
}