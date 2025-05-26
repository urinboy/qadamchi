<?php
/**
 * Qadamchi framework – Dastlabki o‘rnatish skripti
 * Bu skript ishlatilganda, framework uchun kerakli barcha papka va fayllarni o‘zbek tilida yaratadi.
 */

// Papkalarni yaratish funksiyasi
function papkaYarat($yol) {
    if (!is_dir($yol)) {
        mkdir($yol, 0777, true);
        echo "Yaratildi: $yol\n";
    }
}

// Fayl yaratish funksiyasi
function faylYarat($yol, $mazmun = "") {
    if (!file_exists($yol)) {
        file_put_contents($yol, $mazmun);
        echo "Yaratildi: $yol\n";
    }
}

// Papkalar ro‘yxati (tuzilma bo‘yicha)
$papkalar = [
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

// Har bir papkani yaratamiz
foreach ($papkalar as $papka) {
    papkaYarat($papka);
}

// .env namunaviy fayli
faylYarat('.env', <<<EOT
APP_NOMI=Qadamchi
APP_MUHHIT=local
APP_DEBUG=true
DB_HOST=localhost
DB_NOMI=qadamchi
DB_FOYDALANUVCHI=root
DB_PAROL=
EOT
);

// config/app.php
faylYarat('config/app.php', <<<EOT
<?php
return [
    'nomi' => 'Qadamchi',
    'muhhit' => 'local',
    'debug' => true,
];
EOT
);

// config/db.php
faylYarat('config/db.php', <<<EOT
<?php
return [
    'host' => 'localhost',
    'nomi' => 'qadamchi',
    'foydalanuvchi' => 'root',
    'parol' => '',
];
EOT
);

// core/Route.php
faylYarat('core/Route.php', <<<EOT
<?php
class Route {
    protected static \$yo‘llar = [];

    public static function get(\$url, \$amaliyot) {
        self::\$yo‘llar['GET'][\$url] = \$amaliyot;
    }
    public static function post(\$url, \$amaliyot) {
        self::\$yo‘llar['POST'][\$url] = \$amaliyot;
    }
    public static function dispatch() {
        \$usul = \$_SERVER['REQUEST_METHOD'];
        \$url = parse_url(\$_SERVER['REQUEST_URI'], PHP_URL_PATH);
        \$amaliyot = self::\$yo‘llar[\$usul][\$url] ?? null;
        if (\$amaliyot) {
            if (is_callable(\$amaliyot)) {
                return call_user_func(\$amaliyot);
            } elseif (is_string(\$amaliyot) && strpos(\$amaliyot, '@')) {
                list(\$controller, \$metod) = explode('@', \$amaliyot);
                \$controller = "App\\\\Controllers\\\\\$controller";
                (new \$controller)->{\$metod}();
            }
        } else {
            http_response_code(404);
            echo "404 – Topilmadi";
        }
    }
}
EOT
);

// core/Controller.php
faylYarat('core/Controller.php', <<<EOT
<?php
abstract class Controller {
    protected function view(\$nom, \$param = []) {
        extract(\$param);
        require __DIR__ . '/../app/Views/' . \$nom . '.php';
    }
    protected function yo‘naltir(\$url) {
        header('Location: ' . \$url);
        exit;
    }
}
EOT
);

// core/Model.php
faylYarat('core/Model.php', <<<EOT
<?php
abstract class Model {
    // Bu yerda oddiy PDO orqali bazaga ulanish va so'rovlar yoziladi (hozircha bo'sh)
}
EOT
);

// core/View.php
faylYarat('core/View.php', <<<EOT
<?php
class View {
    public static function chiqar(\$nom, \$param = []) {
        extract(\$param);
        require __DIR__ . '/../app/Views/' . \$nom . '.php';
    }
}
EOT
);

// core/Middleware.php
faylYarat('core/Middleware.php', <<<EOT
<?php
abstract class Middleware {
    abstract public function ishlash(\$so‘rov, \$keyingi);
}
EOT
);

// core/Request.php
faylYarat('core/Request.php', <<<EOT
<?php
class Request {
    public static function get(\$nom, \$standart = null) {
        return \$_GET[\$nom] ?? \$standart;
    }
    public static function post(\$nom, \$standart = null) {
        return \$_POST[\$nom] ?? \$standart;
    }
}
EOT
);

// core/Response.php
faylYarat('core/Response.php', <<<EOT
<?php
class Response {
    public static function json(\$malumot) {
        header('Content-Type: application/json');
        echo json_encode(\$malumot, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
EOT
);

// core/Validator.php
faylYarat('core/Validator.php', <<<EOT
<?php
class Validator {
    public static function tekshir(\$malumot, \$qoidalar) {
        \$xatolar = [];
        foreach (\$qoidalar as \$maydon => \$q) {
            if (\$q === 'majburiy' && empty(\$malumot[\$maydon])) {
                \$xatolar[\$maydon][] = 'Majburiy maydon';
            }
        }
        return \$xatolar;
    }
}
EOT
);

// core/Session.php
faylYarat('core/Session.php', <<<EOT
<?php
class Session {
    public static function qoy(\$kalit, \$qiymat) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        \$_SESSION[\$kalit] = \$qiymat;
    }
    public static function ol(\$kalit, \$standart = null) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        return \$_SESSION[\$kalit] ?? \$standart;
    }
}
EOT
);

// core/Auth.php
faylYarat('core/Auth.php', <<<EOT
<?php
class Auth {
    public static function kirish(\$foydalanuvchi) {
        Session::qoy('foydalanuvchi', \$foydalanuvchi);
    }
    public static function foydalanuvchi() {
        return Session::ol('foydalanuvchi');
    }
    public static function chiqish() {
        Session::qoy('foydalanuvchi', null);
    }
    public static function tekshir() {
        return Session::ol('foydalanuvchi') ? true : false;
    }
}
EOT
);

// core/ErrorHandler.php
faylYarat('core/ErrorHandler.php', <<<EOT
<?php
class ErrorHandler {
    public static function xato(\$xatolik) {
        echo "Xatolik: " . \$xatolik->getMessage();
    }
}
EOT
);

// core/Logger.php
faylYarat('core/Logger.php', <<<EOT
<?php
class Logger {
    public static function yoz(\$xabar) {
        \$fayl = __DIR__ . '/../storage/logs/qadamchi.log';
        \$sana = date('Y-m-d H:i:s');
        file_put_contents(\$fayl, "[\$sana] \$xabar\n", FILE_APPEND);
    }
}
EOT
);

// core/Lang.php
faylYarat('core/Lang.php', <<<EOT
<?php
class Lang {
    public static function ol(\$kalit, \$almashtirish = []) {
        // resources/lang/uz.php yoki app/Lang/uz.php dan olib ishlatish mumkin
        return \$kalit;
    }
}
EOT
);

// core/Migration.php
faylYarat('core/Migration.php', <<<EOT
<?php
abstract class Migration {
    abstract public function yuqoriga();
    abstract public function pastga();
}
EOT
);

// core/Seeder.php
faylYarat('core/Seeder.php', <<<EOT
<?php
abstract class Seeder {
    abstract public function ishgaTushur();
}
EOT
);

// routes/web.php
faylYarat('routes/web.php', <<<EOT
<?php
Route::get('/', function() {
    echo "Qadamchi frameworkga xush kelibsiz!";
});
EOT
);

// routes/api.php
faylYarat('routes/api.php', <<<EOT
<?php
// API uchun marshrutlar shu yerga yoziladi
EOT
);

// public/index.php
faylYarat('public/index.php', <<<EOT
<?php
// Avtoloader (soddalashtirilgan)
spl_autoload_register(function (\$class) {
    \$class = str_replace('\\\\', '/', \$class);
    if (file_exists(__DIR__ . '/../core/' . basename(\$class) . '.php')) {
        require __DIR__ . '/../core/' . basename(\$class) . '.php';
    }
    if (file_exists(__DIR__ . '/../app/Controllers/' . basename(\$class) . '.php')) {
        require __DIR__ . '/../app/Controllers/' . basename(\$class) . '.php';
    }
});

// Router va marshrutlar
require __DIR__ . '/../core/Route.php';
require __DIR__ . '/../routes/web.php';

// Marshrutlarni ishga tushurish
Route::dispatch();
EOT
);

// Namuna Controller
faylYarat('app/Controllers/BoshSahifaController.php', <<<EOT
<?php
namespace App\Controllers;

class BoshSahifaController extends \Controller {
    public function index() {
        \$this->view('bosh', ['sarlavha' => 'Qadamchi Framework']);
    }
}
EOT
);

// Namuna View
faylYarat('app/Views/bosh.php', <<<EOT
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <title><?= \$sarlavha ?? "Qadamchi" ?></title>
</head>
<body>
    <h1>Qadamchi Frameworkga hush kelibsiz!</h1>
    <p>Endi siz o‘z loyihangizni yaratishni boshlashingiz mumkin.</p>
</body>
</html>
EOT
);

echo "\nBarcha papka va fayllar o‘zbek tilida yaratildi! Endi 'public/index.php' ni web root sifatida ishga tushirsangiz bo‘ladi.\n";
?>