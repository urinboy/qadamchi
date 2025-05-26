<?php
/**
 * View render qilish uchun klass
 */
class View {
    public static function render($name, $params = []) {
        extract($params);
        require __DIR__ . '/../app/Views/' . $name . '.php';
    }
}