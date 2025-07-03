<?php

declare(strict_types=1);

namespace Modules\Backup\Actions;

use BitCore\Application\Actions\Action;
use Modules\Backup\Repositories\BackupRepositoryInterface;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Action to list all backups.
 *
 * Endpoint: GET /api/backups
 */
class ListBackupsAction extends Action
{
    public function action(): Response
    {
        $repo = $this->container->get(BackupRepositoryInterface::class);
        $backups = $repo->fetchWithFilters($this->request->getQueryParams());
        return $this->respondWithData([
            'status' => true,
            'backups' => $backups,
        ]);
    }
} 