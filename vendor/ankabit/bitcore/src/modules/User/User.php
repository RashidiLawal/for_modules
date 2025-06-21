<?php

namespace BitCore\Modules\User;

use BitCore\Application\Services\Modules\AbstractModule;
use BitCore\Foundation\Container;

/**
 * Class UserModule
 *
 * This class represents the User module, responsible for managing user information including authentication.
 */
class User extends AbstractModule
{
    protected $id = 'User';
    protected $name = 'User Module';
    protected $description = 'Module to manage user\'s information including auth';
    protected $version = '1.0.0'; // Required attribute
    protected $authorName = 'Hammed Madandola';
    protected $authorUrl = 'mailto:hammed@ankabit.com';
    protected $priority = 20; // Load priority. Higher value to load earlier than other modules
    protected $autoloadRegister = false;
    protected $autoloadRoute = false;
    protected $status = 'active';

    /**
     * Entry function to the module.
     * Load dependency into container, load routes, e.t.c
     *
     * @return void
     */
    public function register(): void
    {

        // Inject repositories to container
        $repositoryProvider = require __DIR__ . '/Config/repositories.php';
        $repositoryProvider($this->container);

        /** Load services, route and dependencies using direct inclusion method */
        require __DIR__ . '/Config/dependencies.php';

        // Load languages if your module is using one.
        $this->loadLanguage();
    }
}
