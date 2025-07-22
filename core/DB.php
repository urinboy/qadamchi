<?php

// core/DB.php - yangilash
class DB {
    private static $instance = null;
    
    public static function connection() {
        if (!self::$instance) {
            $config = require __DIR__.'/../config/db.php';
            $dsn = "mysql:host={$config['host']};dbname={$config['name']};charset=utf8mb4";
            self::$instance = new PDO($dsn, $config['user'], $config['pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        }
        return self::$instance;
    }
    
    public static function query($sql, $params = []) {
        $stmt = self::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}

// class DB {
//     public static $pdo = null;

//     public static function pdo() {
//         if (self::$pdo) return self::$pdo;
//         $db = require __DIR__.'/../config/db.php';
//         $dsn = "{$db['driver']}:host={$db['host']};dbname={$db['name']};charset={$db['charset']}";
//         try {
//             self::$pdo = new PDO($dsn, $db['user'], $db['pass']);
//             self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//         } catch(Exception $e) {
//             die("DB ulanishda xatolik: " . $e->getMessage());
//         }
//         return self::$pdo;
//     }
// }