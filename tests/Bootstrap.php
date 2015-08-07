<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

date_default_timezone_set('UTC');

if (in_array('Composer\\Autoload\\ClassLoader', get_declared_classes())) {
    return true;
}

$files = [
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../../../autoload.php',
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $loader = require $file;

        break;
    }
}

if (! is_object($loader)) {
    throw new RuntimeException('vendor/autoload.php could not be found. Did you run `php composer.phar install`?');
}

/* @var $loader \Composer\Autoload\ClassLoader */
// $loader->add('ZfcDatagridTest\\', __DIR__);

