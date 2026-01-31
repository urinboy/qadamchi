<?php
abstract class Controller {
    protected function view($name, $params = [], $layout = 'app') {
        extract($params);
        ob_start();
        require __DIR__ . '/../app/Views/' . $name . '.php';
        $content = ob_get_clean();
        require __DIR__ . '/../app/Views/layouts/' . $layout . '.php';
    }

    protected function redirect($url) {
        header('Location: ' . $url);
        exit;
    }
}