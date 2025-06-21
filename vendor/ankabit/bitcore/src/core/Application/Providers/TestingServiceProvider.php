<?php

declare(strict_types=1);

namespace BitCore\Application\Providers;

use BitCore\Application\Providers\ProviderAbstract;
use BitCore\Application\Services\Modules\ModuleRegistry;

/**
 * Registers test core services and components within the application container.
 *
 * Majorly run migration and ensure everything in place when running tests
 */
class TestingServiceProvider extends ProviderAbstract
{
    /**
     * {@inheritDoc}
     */
    public function boot()
    {
        // We only want to run this in test environtment only
        if (env('APP_ENV') !== 'test') {
            return;
        }

        $container = $this->app->getContainer();

        // Ensure migration repository exists
        $migrator = $container->get('migrator');
        $repository = $migrator->getRepository();

        if (!$repository->repositoryExists()) {
            $repository->createRepository();
        }

        // Run migrations from core and module-specific paths
        $migrator->run(
            array_merge(
                [__DIR__ . '/../src/core/Application/Database/Migrations'],
                $container->get(ModuleRegistry::class)->getMigrationPaths()
            )
        );
    }
}
