<?php

namespace Modules\Affiliate;

use BitCore\Application\Services\Modules\AbstractModule;
use BitCore\Foundation\Container;

/**
 * Class AffiliateModule
 *
 * This class represents the Affiliate module, responsible for managing Affiliate information including authentication.
 */
class Affiliate extends AbstractModule
{
    protected $id = 'Affiliate';
    protected $name = 'Affiliate Module';
    protected $description = 'Module to manage Affiliate\'s information including auth';
    protected $version = '1.0.0'; // Required attribute
    protected $authorName = 'Aliu Adedigba';
    protected $authorUrl = 'mailto:ali@ankabit.com';
    protected $priority = 20; // Load priority. Higher value to load earlier than other modules
    protected $autoloadRegister = true;
}
