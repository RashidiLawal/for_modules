<?php
declare(strict_types=1);

// Tell bitcore about your base directory for the project
defined('APP_BASE_PATH') or define('APP_BASE_PATH', realpath(__DIR__ . '/../') . '/');

// Tell bitcore your config folder for autoloading of default config overrides.
defined('APP_CONFIG_PATH') or define('APP_CONFIG_PATH', APP_BASE_PATH . 'src/config/');

// Tell bitcore where you plan to store your modules and the namespace for module loading.
//We are using Modules\ namespace and will be added to the composer.json psr4
defined('APP_MODULES_PATH') or define('APP_MODULES_PATH', APP_BASE_PATH . 'src/modules/');
defined('APP_MODULES_BASE_NAMESPACE') or define('APP_MODULES_BASE_NAMESPACE', 'Modules\\');

// Optionally include index from bitcore to handle request (PSR7) out of the box
require __DIR__ . '/../vendor/ankabit/bitcore/public/index.php';
