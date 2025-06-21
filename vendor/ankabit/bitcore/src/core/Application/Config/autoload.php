<?php

declare(strict_types=1);

require_once __DIR__ . "/constants.php";
require_once __DIR__ . "/../Helpers/common.php";

/**
 * Autoload for modules where necessary.
 * Modules autoload file should contain global functions or expression
 * that is independent on module active state as the file will alway be loaded
 * even when the module is disabled.
 */
foreach (get_module_path_namespace_map() as $modulesPath => $baseNamespace) {
    foreach (glob($modulesPath . '*/Config/autoload.php') as $autoloadPath) {
        require_once $autoloadPath;
    }
}
