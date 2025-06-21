<?php

declare(strict_types=1);

namespace BitCore\Application\Actions;

use BitCore\Application\Services\Modules\ModuleRegistry;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Handles the application installation process.
 *
 * This action performs necessary installation tasks such as running database migrations.
 */
class InstallAction extends Action
{
    /**
     * Executes the app installation process.
     *
     * @return Response The response to be sent to the client.
     */
    protected function action(): Response
    {
        $message = 'Installation completed successfully';

        // @todo Check file permissions

        /** @var \BitCore\Foundation\Database\Migrator $migrator */
        $migrator = $this->container->get('migrator');
        $repository = $migrator->getRepository();

        // Create the migration repository table if it doesn't exist
        if (!$repository->repositoryExists()) {
            $repository->createRepository();
        }

        // Run migrations for the core application and all modules
        $migrationPaths = array_merge(
            [__DIR__ . '/../Database/Migrations'],
            $this->container->get(ModuleRegistry::class)->getMigrationPaths()
        );
        $migrator->run($migrationPaths);

        return $this->respondWithData([
            'message' => $message
        ]);
    }
}
