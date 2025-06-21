<?php

namespace BitCore\Modules\Documentation;

use BitCore\Application\Services\Modules\AbstractModule;

/**
 * Class DocumentationModule
 *
 * This class represents the Documentation module
 * responsible for managing Documentation information including authentication.
 */
class Documentation extends AbstractModule
{
    protected $id = 'Documentation';
    protected $name = 'Documentation Module';
    protected $description = 'Module to manage Documentation\'s information including auth';
    protected $version = '1.0.0'; // Required attribute
    protected $authorName = 'Aliu Adedigba';
    protected $authorUrl = 'mailto:ali@ankabit.com';
    protected $priority = 20; // Load priority. Higher value to load earlier than other modules
    protected $autoloadRegister = true;
}
