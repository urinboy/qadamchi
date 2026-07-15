<?php
/**
 * Qadamchi CLI bootstrap — buyruqlar uchun minimal yuklash.
 * autoload + env + helpers + Container/Config (config() ishlashi uchun).
 */
require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/../core/Support/env.php';
load_env();
require_once __DIR__ . '/../core/Support/helpers.php';

use Qadamchi\Container\Container;
use Qadamchi\Support\Config;

$container = new Container();
Container::setInstance($container);
$container->singleton(Config::class, fn() => new Config(base_path('config')));

return $container;