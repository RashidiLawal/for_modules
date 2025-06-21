<?php

declare(strict_types=1);

namespace Bitcore\Modules\ModulesManager;

use BitCore\Application\Services\Modules\AbstractModule;

/**
 * Class ModulesManaager
 *
 * This class represents the ModulesManager module, responsible for managing modules
 */
class ModulesManager extends AbstractModule
{
    protected $id = 'ModulesManager';
    protected $name = 'Module to manage all system modules';
    protected $description = 'Core Module to
     manage all system modules. 
    It handle module upload, activation/deactivate e.t.c';
    protected $version = '1.0.0'; // Required attribute
    protected $authorName = 'Hammed Madandola';
    protected $authorUrl = 'mailto:hammed@ankabit.com';
    protected $autoloadRoute = true;
    protected $autoloadRegister = true;
    protected $autoloadBoot = false;
    protected $priority = PHP_INT_MAX;

    // Tag as core module.
    protected $isCore = true;
}
