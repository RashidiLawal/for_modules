<?php

declare(strict_types=1);

namespace Modules\Backup;

use BitCore\Application\Services\Modules\AbstractModule;

/**
 * Backup Module
 *
 * Provides API endpoints for creating, restoring, and managing backups.
 *
 * @author Rashdidi Lawal
 * @contact devrashlaw@gmail.com
 * @version 1.0.0
 */
class Backup extends AbstractModule
{
    /** @var string Module ID */
    protected $id = 'backup';
    /** @var string Module name */
    protected $name = 'Backup Module';
    /** @var string Module description */
    protected $description = 'Provides API endpoints for creating, restoring, and managing backups.';
    /** @var string Module version */
    protected $version = '1.0.0';
    /** @var string Author name */
    protected $authorName = 'Rashdidi Lawal';
    /** @var string Author contact */
    protected $authorUrl = 'devrashlaw@gmail.com';
    /** @var bool Autoload routes */
    protected $autoloadRoute = true;
    /** @var bool Autoload register */
    protected $autoloadRegister = true;
    /** @var bool Autoload boot */
    protected $autoloadBoot = false;
} 