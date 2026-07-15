<?php
namespace Qadamchi\Routing;

use Qadamchi\Http\Request;
use Qadamchi\Http\Response;
use Qadamchi\Http\Pipeline;
use Qadamchi\Container\Container;
use Qadamchi\Exceptions\RouteNotFoundException;

/**
 * Router — Laravel'ning Route g'oyasi.
 * Parametrli route'lar: /users/{id} -> regex #^/users/([^/]+)$#
 * Middleware pipeline (Container orqali DI), named routes, guruhlar.
 */
class Route
{
    protected static array $routes = [];
    protected static array $namedRoutes = [];
    protected static array $groupStack = [];
    protected static array $globalMiddleware = [];
    protected static array $middlewareAliases = [];
    protected static ?Container $container = null;

    public static function setContainer(Container $container): void
    {
        self::$container = $container;
    }

    public static function setGlobalMiddleware(array $middleware): void
    {
        self::$globalMiddleware = $middleware;
    }

    public static function middlewareAlias(array $aliases): void
    {
        self::$middlewareAliases = array_merge(self::$middlewareAliases, $aliases);
    }

    public static function get($uri, $action)    { return self::addRoute(['GET'], $uri, $action); }
    public static function post($uri, $action)   { return self::addRoute(['POST'], $uri, $action); }
    public static function put($uri, $action)    { return self::addRoute(['PUT'], $uri, $action); }
    public static function patch($uri, $action)  { return self::addRoute(['PATCH'], $uri, $action); }
    public static function delete($uri, $action) { return self::addRoute(['DELETE'], $uri, $action); }
    public static function options($uri, $action){ return self::addRoute(['OPTIONS'], $uri, $action); }
    public static function any($uri, $action)    { return self::addRoute(['GET','POST','PUT','PATCH','DELETE','OPTIONS'], $uri, $action); }
    public static function match(array $methods, $uri, $action) { return self::addRoute($methods, $uri, $action); }

    public static function group(array $attributes, $callback): void
    {
        self::$groupStack[] = $attributes;
        $callback();
        array_pop(self::$groupStack);
    }

    protected static function addRoute(array $methods, string $uri, $action): RouteRegistrar
    {
        $uri = self::applyGroupPrefix($uri);

        // {param} -> regex, param nomlarini yig'amiz
        $paramNames = [];
        $pattern = preg_replace_callback('/\{([a-zA-Z_][a-zA-Z0-9_]*)\??\}/', function ($m) use (&$paramNames) {
            $paramNames[] = $m[1];
            return '([^/]+)';
        }, $uri);
        $regex = '#^' . $pattern . '$#';

        $route = [
            'uri'         => $uri,
            'methods'     => array_map('strtoupper', $methods),
            'action'      => $action,
            'regex'       => $regex,
            'params'      => $paramNames,
            'name'        => null,
            'middleware'  => self::collectMiddleware(),
        ];

        self::$routes[] = &$route;
        $id = count(self::$routes) - 1;
        return new RouteRegistrar($route, $id);
    }

    protected static function applyGroupPrefix(string $uri): string
    {
        $prefix = '';
        foreach (self::$groupStack as $group) {
            if (isset($group['prefix'])) {
                $prefix .= '/' . trim($group['prefix'], '/');
            }
        }
        return $prefix . $uri;
    }

    protected static function collectMiddleware(): array
    {
        $middlewares = [];
        foreach (self::$groupStack as $group) {
            if (isset($group['middleware'])) {
                foreach ((array) $group['middleware'] as $mw) {
                    $middlewares[] = $mw;
                }
            }
        }
        return $middlewares;
    }

    public static function setName(int $id, string $name): void
    {
        if (isset(self::$routes[$id])) {
            self::$routes[$id]['name'] = $name;
            self::$namedRoutes[$name] = self::$routes[$id]['uri'];
        }
    }

    public static function addMiddleware(int $id, $middleware): void
    {
        if (isset(self::$routes[$id])) {
            foreach ((array) $middleware as $mw) {
                self::$routes[$id]['middleware'][] = $mw;
            }
        }
    }

    /** Barcha ro'yxatdan o'tgan route'lar (route:list uchun). */
    public static function routes(): array
    {
        return self::$routes;
    }

    public static function namedRoutes(): array
    {
        return self::$namedRoutes;
    }

    /** So'rovni dispatch qiladi — Response qaytaradi (send() qilmaydi). */
    public static function dispatch(?Request $request = null): Response
    {
        $request = $request ?? Request::instance();
        Request::setInstance($request);

        $method = $request->method();
        $path = $request->path();

        foreach (self::$routes as $route) {
            if (!in_array($method, $route['methods'], true)) {
                continue;
            }
            if (!preg_match($route['regex'], $path, $matches)) {
                continue;
            }

            // Parametrlarni extrakt qilamiz
            $params = [];
            foreach ($route['params'] as $i => $name) {
                $params[$name] = $matches[$i + 1] ?? null;
            }
            $request->setRouteParams($params);

            // Middleware to'plamini yig'amiz
            $middleware = self::resolveMiddleware(
                array_merge(self::$globalMiddleware, $route['middleware'], self::controllerMiddleware($route['action'], $params))
            );

            $pipeline = new Pipeline(self::$container);

            return $pipeline
                ->send($request)
                ->through($middleware)
                ->then(function ($request) use ($route, $params) {
                    $response = self::runAction($route['action'], $params);
                    return self::toResponse($response, $request);
                });
        }

        throw new RouteNotFoundException("$method $path");
    }

    protected static function runAction($action, array $params)
    {
        $container = self::$container;

        // Array action: [ControllerClass::class, 'method']
        if (is_array($action) && count($action) === 2 && is_string($action[0])) {
            [$controllerClass, $method] = $action;
            if (!class_exists($controllerClass)) {
                throw new \RuntimeException("Controller topilmadi: $controllerClass");
            }
            return $container
                ? $container->call([$controllerClass, $method], $params)
                : (new $controllerClass())->{$method}(...array_values($params));
        }

        if ($action instanceof \Closure) {
            return $container ? $container->call($action, $params) : call_user_func_array($action, $params);
        }
        if (is_string($action) && strpos($action, '@')) {
            [$controller, $method] = explode('@', $action, 2);
            $controllerClass = "App\\Controllers\\$controller";
            if (!class_exists($controllerClass)) {
                throw new \RuntimeException("Controller topilmadi: $controllerClass");
            }
            return $container
                ? $container->call([$controllerClass, $method], $params)
                : (new $controllerClass())->{$method}(...array_values($params));
        }
        if (is_callable($action)) {
            return call_user_func_array($action, $params);
        }
        throw new \RuntimeException("Noto'g'ri route action: " . print_r($action, true));
    }

    protected static function controllerMiddleware($action, array $params): array
    {
        // Array action
        if (is_array($action) && count($action) === 2 && is_string($action[0])) {
            [$controllerClass, $method] = $action;
            if (!class_exists($controllerClass)) return [];
            $instance = new $controllerClass();
            return self::collectControllerMiddleware($instance, $method);
        }
        // String action Controller@method
        if (is_string($action) && strpos($action, '@')) {
            [$controller, $method] = explode('@', $action, 2);
            $controllerClass = "App\\Controllers\\$controller";
            if (!class_exists($controllerClass)) return [];
            $instance = new $controllerClass();
            return self::collectControllerMiddleware($instance, $method);
        }
        return [];
    }

    protected static function collectControllerMiddleware($instance, string $method): array
    {
        $middlewares = [];
        foreach (($instance->getMiddleware() ?? []) as $entry) {
            $options = $entry['options'] ?? [];
            $only = $options['only'] ?? null;
            $except = $options['except'] ?? null;
            if ($only && !in_array($method, (array) $only, true)) continue;
            if ($except && in_array($method, (array) $except, true)) continue;
            $middlewares[] = $entry['class'];
        }
        return $middlewares;
    }

    protected static function resolveMiddleware(array $middleware): array
    {
        $resolved = [];
        foreach ($middleware as $mw) {
            $resolved[] = self::$middlewareAliases[$mw] ?? $mw;
        }
        return $resolved;
    }

    protected static function toResponse($response, Request $request): Response
    {
        if ($response instanceof Response) {
            return $response;
        }
        if ($response === null) {
            return Response::make('', 200);
        }
        if (is_array($response)) {
            return Response::json($response);
        }
        if (is_string($response)) {
            return Response::make($response, 200);
        }
        if (is_object($response) && method_exists($response, '__toString')) {
            return Response::make((string) $response, 200);
        }
        return Response::make((string) $response, 200);
    }

    /** Named route'dan URL olish: route('users.show', ['id'=>5]) -> /users/5 */
    public static function url(string $name, array $params = []): ?string
    {
        if (!isset(self::$namedRoutes[$name])) {
            return null;
        }
        $uri = self::$namedRoutes[$name];
        foreach ($params as $key => $val) {
            $uri = preg_replace('/\{' . preg_quote($key, '/') . '\??\}/', urlencode((string) $val), $uri);
        }
        return $uri;
    }
}