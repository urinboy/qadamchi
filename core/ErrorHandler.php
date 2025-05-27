<?php
class ErrorHandler {
    public static function show($code, $message = null) {
        http_response_code($code);
        $view = __DIR__ . '/../app/Views/errors/' . $code . '.php';
        if (file_exists($view)) {
            include $view;
        } else {
            include __DIR__ . '/../app/Views/errors/default.php';
        }
        exit;
    }

    public static function handle($exception) {
        self::show(500, $exception->getMessage());
    }
}