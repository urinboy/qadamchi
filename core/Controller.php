<?php
abstract class Controller {
    protected function view($name, $params = [], $layout = 'app') {
        extract($params);
        if ($layout) {
            ob_start();
            require __DIR__ . '/../app/Views/' . $name . '.php';
            $content = ob_get_clean();
            require __DIR__ . '/../app/Views/layouts/' . $layout . '.php';
        } else {
            require __DIR__ . '/../app/Views/' . $name . '.php';
        }
    }

    protected function redirect($url) {
        header('Location: ' . $url);
        exit;
    }
}