<?php

declare(strict_types=1);

namespace BitCore\Application\Providers;

use BitCore\Application\Providers\ProviderAbstract;

/**
 * Load and provide routes for the core endpoints only.
 * Modules routes should be handled in each module
 */
class RoutesProvider extends ProviderAbstract
{
    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $routes = read_config_array('routes.php');
        $routes($this->app);
    }
}
