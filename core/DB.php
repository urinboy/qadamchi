<?php
class DB {
    public static $pdo = null;

    public static function pdo() {
        if (self::$pdo) return self::$pdo;
        $db = require __DIR__.'/../config/db.php';
        $dsn = "{$db['driver']}:host={$db['host']};dbname={$db['name']};charset={$db['charset']}";
        try {
            self::$pdo = new PDO($dsn, $db['user'], $db['pass']);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(Exception $e) {
            die("DB ulanishda xatolik: " . $e->getMessage());
        }
        return self::$pdo;
    }
}