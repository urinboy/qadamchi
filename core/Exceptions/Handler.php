<?php
namespace Qadamchi\Exceptions;

use Exception;
use ErrorException;
use Qadamchi\Support\Logger;

/**
 * Yagona exception/error handler (eski takroriy ErrorHandler'larni almashtiradi).
 * bootstrap/app.php da Handler::register() orqali ro'yxatdan o'tadi.
 */
class Handler
{
    protected static ?Logger $logger = null;

    public static function setLogger(?Logger $logger): void
    {
        self::$logger = $logger;
    }

    public static function register(): void
    {
        set_exception_handler([self::class, 'handleException']);
        set_error_handler([self::class, 'handleError']);
        register_shutdown_function([self::class, 'handleShutdown']);
    }

    public static function handleError($level, $message, $file = '', $line = 0): bool
    {
        if (error_reporting() & $level) {
            throw new ErrorException($message, 0, $level, $file, $line);
        }
        return false;
    }

    public static function handleException(\Throwable $e): void
    {
        if ($e instanceof QadamchiException) {
            $e->report(self::$logger);
        } else {
            self::log($e);
        }

        // Validatsiya xatosi — old input + xatolar flash qilingan, redirect back.
        if ($e instanceof ValidationException) {
            $referer = $_SERVER['HTTP_REFERER'] ?? '/';
            \Qadamchi\Http\Response::redirect($referer, 302)->send();
            return;
        }

        if (self::isDebug()) {
            self::renderDebug($e);
        } else {
            $code = self::httpCode($e);
            self::renderError($code, $e->getMessage());
        }
    }

    public static function handleShutdown(): void
    {
        $error = error_get_last();
        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
            self::handleException(new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']));
        }
    }

    public static function show(int $code, string $message = ''): void
    {
        http_response_code($code);
        self::renderError($code, $message);
        exit;
    }

    protected static function isDebug(): bool
    {
        return (bool) (function_exists('env') ? env('APP_DEBUG', false) : false);
    }

    protected static function httpCode(\Throwable $e): int
    {
        if ($e instanceof RouteNotFoundException) return 404;
        if ($e instanceof ValidationException) return 422;
        return 500;
    }

    protected static function log(\Throwable $e): void
    {
        if (self::$logger) {
            self::$logger->error('{message} in {file}:{line}', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
        }
    }

    protected static function renderError(int $code, string $message = ''): void
    {
        http_response_code($code);
        try {
            // Blade view: errors/{code}.blade.php yoki errors/default.blade.php
            $view = 'errors.' . $code;
            if (is_file(resource_path('views/errors/' . $code . '.blade.php'))) {
                echo \Qadamchi\View\View::render($view, ['code' => $code, 'message' => $message]);
                return;
            }
            if (is_file(resource_path('views/errors/default.blade.php'))) {
                echo \Qadamchi\View\View::render('errors.default', ['code' => $code, 'message' => $message]);
                return;
            }
        } catch (\Throwable $e) {
            // view tizimi ishlamasa — sodda matn
        }
        echo "Xatolik $code" . ($message !== '' ? ": $message" : '');
    }

    protected static function renderDebug(\Throwable $e): void
    {
        http_response_code(self::httpCode($e));
        try {
            if (is_file(resource_path('views/errors/debug.blade.php'))) {
                echo \Qadamchi\View\View::render('errors.debug', ['exception' => $e]);
                return;
            }
        } catch (\Throwable $ignored) {
        }
        echo '<pre>' . htmlspecialchars((string) $e) . '</pre>';
    }
}