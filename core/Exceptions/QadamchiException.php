<?php
abstract class QadamchiException extends Exception {
    public function report() {
        Logger::log($this->getMessage());
    }
}

class ValidationException extends QadamchiException {
    private $errors;
    
    public function __construct($errors) {
        $this->errors = $errors;
        parent::__construct('Validation failed');
    }
    
    public function getErrors() {
        return $this->errors;
    }
}

class RouteNotFoundException extends QadamchiException {
    public function __construct($route) {
        parent::__construct("Route not found: $route");
    }
}

// core/ErrorHandler.php - yangilash
class ErrorHandler {
    public static function register() {
        set_exception_handler([self::class, 'handleException']);
        set_error_handler([self::class, 'handleError']);
    }
    
    public static function handleException($exception) {
        if ($exception instanceof QadamchiException) {
            $exception->report();
        }
        
        if (env('APP_DEBUG', false)) {
            self::renderDebugPage($exception);
        } else {
            self::renderErrorPage(500);
        }
    }
    
    private static function renderDebugPage($exception) {
        $trace = $exception->getTraceAsString();
        include __DIR__ . '/../app/Views/errors/debug.php';
    }
    
    private static function renderErrorPage($code) {
        http_response_code($code);
        $view = __DIR__ . "/../app/Views/errors/$code.php";
        if (file_exists($view)) {
            include $view;
        } else {
            echo "Error $code";
        }
    }
}