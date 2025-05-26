<?php
/**
 * Qadamchi framework – Initial installer script
 * This script creates all necessary directories and files with English structure and comments.
 */

// Directory creation utility
function createDir($path) {
    if (!is_dir($path)) {
        mkdir($path, 0777, true);
        echo "Created: $path\n";
    }
}

// File creation utility
function createFile($path, $content = "") {
    if (!file_exists($path)) {
        file_put_contents($path, $content);
        echo "Created: $path\n";
    }
}

// Directory structure as specified
$dirs = [
    'core',
    'app/Controllers',
    'app/Models',
    'app/Middlewares',
    'app/Views',
    'app/Lang',
    'app/Migrations',
    'app/Seeders',
    'public',
    'routes',
    'config',
    'storage/logs',
    'storage/cache',
    'storage/sessions',
];

foreach ($dirs as $dir) {
    createDir($dir);
}

// .env example file
createFile('.env', <<<EOT
APP_NAME=Qadamchi
APP_ENV=local
APP_DEBUG=true
DB_HOST=localhost
DB_NAME=qadamchi
DB_USER=root
DB_PASS=
EOT
);

// config/app.php
createFile('config/app.php', <<<EOT
<?php
return [
    'name' => 'Qadamchi',
    'env' => 'local',
    'debug' => true,
];
EOT
);

// config/db.php
createFile('config/db.php', <<<EOT
<?php
return [
    'host' => 'localhost',
    'name' => 'qadamchi',
    'user' => 'root',
    'pass' => '',
];
EOT
);

// core/Route.php
createFile('core/Route.php', <<<EOT
<?php
/**
 * Route class handles HTTP route registration and dispatching.
 */
class Route {
    protected static \$routes = [];

    public static function get(\$uri, \$action) {
        self::\$routes['GET'][\$uri] = \$action;
    }
    public static function post(\$uri, \$action) {
        self::\$routes['POST'][\$uri] = \$action;
    }
    // You may add put, delete, etc.

    public static function dispatch() {
        \$method = \$_SERVER['REQUEST_METHOD'];
        \$uri = parse_url(\$_SERVER['REQUEST_URI'], PHP_URL_PATH);
        \$action = self::\$routes[\$method][\$uri] ?? null;
        if (\$action) {
            if (is_callable(\$action)) {
                return call_user_func(\$action);
            } elseif (is_string(\$action) && strpos(\$action, '@')) {
                list(\$controller, \$method) = explode('@', \$action);
                \$controller = "App\\\\Controllers\\\\\$controller";
                (new \$controller)->{\$method}();
            }
        } else {
            http_response_code(404);
            echo "404 – Not found";
        }
    }
}
EOT
);

// core/Controller.php
createFile('core/Controller.php', <<<EOT
<?php
/**
 * Base Controller class for Qadamchi framework.
 */
abstract class Controller {
    protected function view(\$name, \$params = []) {
        extract(\$params);
        require __DIR__ . '/../app/Views/' . \$name . '.php';
    }
    protected function redirect(\$url) {
        header('Location: ' . \$url);
        exit;
    }
}
EOT
);

// core/Model.php
createFile('core/Model.php', <<<EOT
<?php
/**
 * Base Model class (PDO database logic can be placed here).
 */
abstract class Model {
    // Add your database logic here
}
EOT
);

// core/View.php
createFile('core/View.php', <<<EOT
<?php
/**
 * View rendering class for Qadamchi.
 */
class View {
    public static function render(\$name, \$params = []) {
        extract(\$params);
        require __DIR__ . '/../app/Views/' . \$name . '.php';
    }
}
EOT
);

// core/Middleware.php
createFile('core/Middleware.php', <<<EOT
<?php
/**
 * Base Middleware class.
 */
abstract class Middleware {
    abstract public function handle(\$request, \$next);
}
EOT
);

// core/Request.php
createFile('core/Request.php', <<<EOT
<?php
/**
 * Request helper for working with GET/POST data.
 */
class Request {
    public static function get(\$key, \$default = null) {
        return \$_GET[\$key] ?? \$default;
    }
    public static function post(\$key, \$default = null) {
        return \$_POST[\$key] ?? \$default;
    }
}
EOT
);

// core/Response.php
createFile('core/Response.php', <<<EOT
<?php
/**
 * Response helper (for JSON, etc).
 */
class Response {
    public static function json(\$data) {
        header('Content-Type: application/json');
        echo json_encode(\$data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
EOT
);

// core/Validator.php
createFile('core/Validator.php', <<<EOT
<?php
/**
 * Simple Validator class.
 */
class Validator {
    public static function make(\$data, \$rules) {
        \$errors = [];
        foreach (\$rules as \$field => \$rule) {
            if (\$rule === 'required' && empty(\$data[\$field])) {
                \$errors[\$field][] = 'This field is required';
            }
        }
        return \$errors;
    }
}
EOT
);

// core/Session.php
createFile('core/Session.php', <<<EOT
<?php
/**
 * Session helper class.
 */
class Session {
    public static function put(\$key, \$value) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        \$_SESSION[\$key] = \$value;
    }
    public static function get(\$key, \$default = null) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        return \$_SESSION[\$key] ?? \$default;
    }
}
EOT
);

// core/Auth.php
createFile('core/Auth.php', <<<EOT
<?php
/**
 * Simple Auth class.
 */
class Auth {
    public static function login(\$user) {
        Session::put('user', \$user);
    }
    public static function user() {
        return Session::get('user');
    }
    public static function logout() {
        Session::put('user', null);
    }
    public static function check() {
        return Session::get('user') ? true : false;
    }
}
EOT
);

// core/ErrorHandler.php
createFile('core/ErrorHandler.php', <<<EOT
<?php
/**
 * Error handler for Qadamchi framework.
 */
class ErrorHandler {
    public static function handle(\$exception) {
        echo "Error: " . \$exception->getMessage();
    }
}
EOT
);

// core/Logger.php
createFile('core/Logger.php', <<<EOT
<?php
/**
 * Logger class for writing logs to file.
 */
class Logger {
    public static function log(\$message) {
        \$file = __DIR__ . '/../storage/logs/qadamchi.log';
        \$date = date('Y-m-d H:i:s');
        file_put_contents(\$file, "[\$date] \$message\\n", FILE_APPEND);
    }
}
EOT
);

// core/Lang.php
createFile('core/Lang.php', <<<EOT
<?php
/**
 * Language helper class (for localization).
 */
class Lang {
    public static function get(\$key, \$replace = []) {
        // You can load translations from app/Lang/
        return \$key;
    }
}
EOT
);

// core/Migration.php
createFile('core/Migration.php', <<<EOT
<?php
/**
 * Base Migration class.
 */
abstract class Migration {
    abstract public function up();
    abstract public function down();
}
EOT
);

// core/Seeder.php
createFile('core/Seeder.php', <<<EOT
<?php
/**
 * Base Seeder class.
 */
abstract class Seeder {
    abstract public function run();
}
EOT
);

// routes/web.php
createFile('routes/web.php', <<<EOT
<?php
Route::get('/', function() {
    echo "Welcome to Qadamchi framework!";
});
EOT
);

// routes/api.php
createFile('routes/api.php', <<<EOT
<?php
// API routes go here
EOT
);

// public/index.php
createFile('public/index.php', <<<EOT
<?php
// Simple autoloader
spl_autoload_register(function (\$class) {
    \$class = str_replace('\\\\', '/', \$class);
    if (file_exists(__DIR__ . '/../core/' . basename(\$class) . '.php')) {
        require __DIR__ . '/../core/' . basename(\$class) . '.php';
    }
    if (file_exists(__DIR__ . '/../app/Controllers/' . basename(\$class) . '.php')) {
        require __DIR__ . '/../app/Controllers/' . basename(\$class) . '.php';
    }
});

// Load routes
require __DIR__ . '/../core/Route.php';
require __DIR__ . '/../routes/web.php';

// Dispatch routes
Route::dispatch();
EOT
);

// Example Controller
createFile('app/Controllers/HomeController.php', <<<EOT
<?php
namespace App\Controllers;

class HomeController extends \\Controller {
    public function index() {
        \$this->view('home', ['title' => 'Qadamchi Framework']);
    }
}
EOT
);

// Example View
createFile('app/Views/home.php', <<<EOT
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= \$title ?? "Qadamchi" ?></title>
</head>
<body>
    <h1>Welcome to Qadamchi Framework!</h1>
    <p>You can start building your project now.</p>
</body>
</html>
EOT
);

echo "\nAll directories and files were created with English structure and comments! Set 'public/index.php' as your web root to get started.\n";
?>