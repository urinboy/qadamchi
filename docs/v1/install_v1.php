<?php
/**
 * Qadamchi framework – Dastlabki o'rnatish skripti
 * Ishga tushurilganda barcha asosiy papka va fayllarni yaratadi.
 */

function makeDir($path) {
    if (!is_dir($path)) {
        mkdir($path, 0777, true);
        echo "Yaratildi: $path\n";
    }
}

function makeFile($path, $content = "") {
    if (!file_exists($path)) {
        file_put_contents($path, $content);
        echo "Yaratildi: $path\n";
    }
}

// Asosiy papkalar
$dirs = [
    'core',
    'app/Controllers',
    'app/Models',
    'app/Views',
    'app/Middlewares',
    'routes',
    'public',
    'config',
];

foreach ($dirs as $dir) {
    makeDir($dir);
}

// .env fayli (ixtiyoriy)
makeFile('.env', <<<EOT
APP_NAME=Qadamchi
APP_ENV=local
APP_DEBUG=true
DB_HOST=localhost
DB_NAME=qadamchi
DB_USER=root
DB_PASS=
EOT
);

// config/app.php fayli
makeFile('config/app.php', <<<EOT
<?php
return [
    'name' => 'Qadamchi',
    'env' => 'local',
    'debug' => true,
];
EOT
);

// config/database.php fayli
makeFile('config/database.php', <<<EOT
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
makeFile('core/Route.php', <<<EOT
<?php
class Route {
    // Oddiy routelar ro'yxati
    protected static \$routes = [];

    public static function get(\$uri, \$action) {
        self::\$routes['GET'][\$uri] = \$action;
    }
    public static function post(\$uri, \$action) {
        self::\$routes['POST'][\$uri] = \$action;
    }
    // ... boshqa metodlar uchun joy qoldiring

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
            echo "404 Not Found";
        }
    }
}
EOT
);

// core/Controller.php
makeFile('core/Controller.php', <<<EOT
<?php
abstract class Controller {
    protected function render(\$view, \$params = []) {
        extract(\$params);
        require __DIR__ . '/../app/Views/' . \$view . '.php';
    }
    protected function redirect(\$url) {
        header('Location: ' . \$url);
        exit;
    }
}
EOT
);

// core/View.php
makeFile('core/View.php', <<<EOT
<?php
class View {
    public static function render(\$view, \$params = []) {
        extract(\$params);
        require __DIR__ . '/../app/Views/' . \$view . '.php';
    }
}
EOT
);

// core/Model.php
makeFile('core/Model.php', <<<EOT
<?php
abstract class Model {
    // Bu yerda oddiy PDO ulanish va query funksiyalari yoziladi (hozircha bo'sh)
}
EOT
);

// core/Request.php
makeFile('core/Request.php', <<<EOT
<?php
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
makeFile('core/Response.php', <<<EOT
<?php
class Response {
    public static function json(\$data) {
        header('Content-Type: application/json');
        echo json_encode(\$data);
        exit;
    }
}
EOT
);

// core/Middleware.php
makeFile('core/Middleware.php', <<<EOT
<?php
abstract class Middleware {
    abstract public function handle(\$request, \$next);
}
EOT
);

// core/Validator.php
makeFile('core/Validator.php', <<<EOT
<?php
class Validator {
    public static function make(\$data, \$rules) {
        \$errors = [];
        foreach (\$rules as \$field => \$rule) {
            if (\$rule === 'required' && empty(\$data[\$field])) {
                \$errors[\$field][] = 'Majburiy maydon';
            }
        }
        return \$errors;
    }
}
EOT
);

// core/Session.php
makeFile('core/Session.php', <<<EOT
<?php
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

// routes/web.php
makeFile('routes/web.php', <<<EOT
<?php
// Misol uchun oddiy route
Route::get('/', function() {
    echo "Qadamchi frameworkga xush kelibsiz!";
});
EOT
);

// public/index.php
makeFile('public/index.php', <<<EOT
<?php
// Avtoloader (oddiy)
spl_autoload_register(function (\$class) {
    \$class = str_replace('\\\\', '/', \$class);
    if (file_exists(__DIR__ . '/../' . \$class . '.php')) {
        require __DIR__ . '/../' . \$class . '.php';
    }
    if (file_exists(__DIR__ . '/../core/' . basename(\$class) . '.php')) {
        require __DIR__ . '/../core/' . basename(\$class) . '.php';
    }
    if (file_exists(__DIR__ . '/../app/Controllers/' . basename(\$class) . '.php')) {
        require __DIR__ . '/../app/Controllers/' . basename(\$class) . '.php';
    }
});

// Routerni yuklash
require __DIR__ . '/../core/Route.php';
require __DIR__ . '/../routes/web.php';

// Marshrutlarni ishga tushurish
Route::dispatch();
EOT
);

// app/Controllers/HomeController.php (namuna controller)
makeFile('app/Controllers/HomeController.php', <<<EOT
<?php
namespace App\Controllers;

class HomeController extends \Controller {
    public function index() {
        \$this->render('home', ['title' => 'Qadamchi Framework']);
    }
}
EOT
);

// app/Views/home.php (namuna view)
makeFile('app/Views/home.php', <<<EOT
<!DOCTYPE html>
<html>
<head>
    <title><?= \$title ?></title>
</head>
<body>
    <h1>Qadamchi Frameworkga xush kelibsiz!</h1>
</body>
</html>
EOT
);

echo "\nBarcha kerakli papka va fayllar yaratildi. Endi 'public/index.php' ni web root sifatida ishga tushirsangiz bo'ladi.\n";
?>