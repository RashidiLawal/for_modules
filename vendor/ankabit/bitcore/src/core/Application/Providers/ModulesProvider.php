<?php

declare(strict_types=1);

namespace BitCore\Application\Providers;

use BitCore\Application\Services\Modules\ModuleRegistry;
use BitCore\Foundation\Container;
use BitCore\Application\Providers\ProviderAbstract;

/**
 * Provide modules manager and load the modules registery.
 * This requires the events provider to be loaded and
 * other important services like DB to check for activated modules.
 */
class ModulesProvider extends ProviderAbstract
{
    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $container = $this->app->getContainer();

        // Register Validator
        $container->singleton(ModuleRegistry::class, function (Container $container) {
            $moduleManager = new ModuleRegistry($container, get_module_path_namespace_map());
            $moduleManager->registerModules($container);
            return $moduleManager;
        });
    }

    /**
     * {@inheritDoc}
     */
    public function boot()
    {
        $container = $this->app->getContainer();
        // @todo Only load active modules
        $container->get(ModuleRegistry::class)->bootModules($container);
    }
}
