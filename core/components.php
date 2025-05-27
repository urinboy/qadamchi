<?php
// Komponentni render qilish uchun funksiya:
function component($name, $data = [], $slot = null) {
    $file = __DIR__ . '/../views/components/' . $name . '.php';
    if (!file_exists($file)) throw new Exception("Component not found: $name");
    extract($data);
    ob_start();
    include $file;
    $content = ob_get_clean();
    if ($slot !== null) {
        $content = str_replace('{{ \$slot }}', $slot, $content);
    }
    return $content;
}