<?php

declare(strict_types=1);

// Ends with trailing slash '/'
defined('BITCORE_BASE_PATH') or define('BITCORE_BASE_PATH', realpath(__DIR__ . '/../../../../') . '/');

defined('BITCORE_CONFIG_PATH') or define('BITCORE_CONFIG_PATH', BITCORE_BASE_PATH . 'src/core/Application/Config/');
defined('BITCORE_LANG_PATHS') or define('BITCORE_LANG_PATHS', [BITCORE_BASE_PATH . 'src/core/Application/lang']);
