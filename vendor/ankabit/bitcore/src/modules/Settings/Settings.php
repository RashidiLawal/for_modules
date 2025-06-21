<?php

declare(strict_types=1);

namespace BitCore\Modules\Settings;

use BitCore\Application\Services\Modules\AbstractModule;

/**
 * Class Settings
 *
 * This class represents the Settings module, responsible for managing settings and setup.
 */
class Settings extends AbstractModule
{
    protected $id = 'Settings';
    protected $name = 'Settings Module';
    protected $description = 'Manage settings and setup with api';
    protected $version = '1.0.0'; // Required attribute
    protected $authorName = 'Hammed Madandola';
    protected $authorUrl = 'mailto:hammed@ankabit.com';
    protected $autoloadRoute = true;
    protected $autoloadRegister = true;
    protected $autoloadBoot = true;
    protected $priority = PHP_INT_MAX;
}
