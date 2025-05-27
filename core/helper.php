<?php
if (!function_exists('route')) {
    function route($name, $params = [])
    {
        return Route::url($name, $params);
    }
}