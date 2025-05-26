<?php
/**
 * Marshrutlar va dispatch uchun Route klassi
 */
class Route {
    protected static $routes = [];
    public static function get($uri, $action) {
        self::$routes['GET'][$uri] = $action;
    }
    public static function post($uri, $action) {
        self::$routes['POST'][$uri] = $action;
    }
    public static function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $action = self::$routes[$method][$uri] ?? null;
        if ($action) {
            if (is_callable($action)) {
                return call_user_func($action);
            } elseif (is_string($action) && strpos($action, '@')) {
                list($controller, $method) = explode('@', $action);
                $controller = "App\\Controllers\\$controller";
                (new $controller)->{$method}();
            }
        } else {
            http_response_code(404);
            echo "404 – Not found";
        }
    }
}