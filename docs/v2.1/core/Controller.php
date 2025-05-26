<?php
/**
 * Barcha controllerlar uchun asos
 */
abstract class Controller {
    protected function view($name, $params = []) {
        extract($params);
        require __DIR__ . '/../app/Views/' . $name . '.php';
    }
    protected function redirect($url) {
        header('Location: ' . $url);
        exit;
    }
}