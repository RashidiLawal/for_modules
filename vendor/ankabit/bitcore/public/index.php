<?php

if (!class_exists(\Composer\Autoload\ClassLoader::class)) {
    require __DIR__ . '/../vendor/autoload.php';
}

require_once __DIR__ . '/../src/core/Kernel/Bootstrap.php';

use BitCore\Kernel\Bootstrap;

$app = new Bootstrap();
$app->run();
