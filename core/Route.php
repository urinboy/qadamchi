<?php

class Route
{
    protected static $routes = [];
    protected static $namedRoutes = [];
    protected static $groupStack = [];

    /** Asosiy metodlar */
    public static function get($uri, $action)           { return self::addRoute(['GET'], $uri, $action); }
    public static function post($uri, $action)          { return self::addRoute(['POST'], $uri, $action); }
    public static function put($uri, $action)           { return self::addRoute(['PUT'], $uri, $action); }
    public static function patch($uri, $action)         { return self::addRoute(['PATCH'], $uri, $action); }
    public static function delete($uri, $action)        { return self::addRoute(['DELETE'], $uri, $action); }
    public static function options($uri, $action)       { return self::addRoute(['OPTIONS'], $uri, $action); }
    public static function any($uri, $action)           { return self::addRoute(['GET','POST','PUT','PATCH','DELETE','OPTIONS'], $uri, $action); }
    public static function match($methods, $uri, $action) { return self::addRoute($methods, $uri, $action); }

    /** Guruhlash (prefix, middleware) */
    public static function group(array $attributes, $callback)
    {
        self::$groupStack[] = $attributes;
        $callback();
        array_pop(self::$groupStack);
    }

    /** Route ni ro‘yxatga qo‘shadi */
    protected static function addRoute($methods, $uri, $action)
    {
        $uri = self::applyGroupPrefix($uri);
        $route = [
            'uri'        => $uri,
            'action'     => $action,
            'methods'    => array_map('strtoupper', $methods),
            'name'       => null,
            'middleware' => self::collectMiddleware(),
        ];
        $id = uniqid('route_', true);
        foreach ($route['methods'] as $method) {
            self::$routes[$method][$uri] = $route;
            self::$routes[$method][$uri]['_id'] = $id;
        }
        return new RouteRegistrar($id);
    }

    /** group prefixini qo‘llash */
    protected static function applyGroupPrefix($uri)
    {
        $prefix = '';
        foreach (self::$groupStack as $group) {
            if (isset($group['prefix'])) {
                $prefix .= rtrim($group['prefix'], '/');
            }
        }
        return $prefix . $uri;
    }

    /** group middlewarelarni yig‘ish */
    protected static function collectMiddleware()
    {
        $middlewares = [];
        foreach (self::$groupStack as $group) {
            if (isset($group['middleware'])) {
                foreach ((array)$group['middleware'] as $mw) {
                    $middlewares[] = $mw;
                }
            }
        }
        return $middlewares;
    }

    /** Route name() funksiyasi uchun */
    public static function setName($id, $name)
    {
        foreach (self::$routes as $method => $routes) {
            foreach ($routes as $uri => &$route) {
                if (isset($route['_id']) && $route['_id'] === $id) {
                    $route['name'] = $name;
                    self::$namedRoutes[$name] = ['method' => $method, 'uri' => $uri];
                }
            }
        }
    }

    /** Route uchun middleware() funksiyasi */
    public static function addMiddleware($id, $middleware)
    {
        foreach (self::$routes as $method => $routes) {
            foreach ($routes as $uri => &$route) {
                if (isset($route['_id']) && $route['_id'] === $id) {
                    $route['middleware'][] = $middleware;
                }
            }
        }
    }

    /** Route dispatch (URL ni tekshirib, controller/method yoki closure ga yo'naltiradi) */
    public static function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        $route = self::$routes[$method][$uri] ?? null;
        if (!$route) {
            // agar any() bo‘lsa
            $route = self::$routes['GET'][$uri] ?? null;
        }
        if ($route) {
            // Middleware larni ishlatish
            foreach ($route['middleware'] as $middleware) {
                if (class_exists($middleware)) {
                    $mwObj = new $middleware;
                    if (method_exists($mwObj, 'handle')) {
                        $mwObj->handle();
                    }
                }
            }
            $action = $route['action'];
            if (is_callable($action)) {
                return call_user_func($action);
            } elseif (is_string($action) && strpos($action, '@')) {
                list($controller, $method) = explode('@', $action);
                $controllerClass = "App\\Controllers\\$controller";
                if (class_exists($controllerClass)) {
                    (new $controllerClass)->{$method}();
                } else {
                    echo "Controller topilmadi: $controllerClass";
                }
            }
        } else {
            http_response_code(404);
            ErrorHandler::show(404);
        }
    }

    /** Route nomidan URL olish */
    public static function url($name, $params = [])
    {
        if (!isset(self::$namedRoutes[$name])) return null;
        $uri = self::$namedRoutes[$name]['uri'];
        // Parametrlarni almashtirish
        if ($params) {
            foreach ($params as $key => $val) {
                $uri = preg_replace("/\{".$key."\}/", $val, $uri);
            }
        }
        return $uri;
    }
}

/** RouteRegistrar: name(), middleware() kabilar uchun yordamchi */
class RouteRegistrar
{
    protected $id;
    public function __construct($id)         { $this->id = $id; }
    public function name($name)              { Route::setName($this->id, $name); return $this; }
    public function middleware($middleware)  { Route::addMiddleware($this->id, $middleware); return $this; }
}