<?php

declare(strict_types=1);

namespace Modules\Backup\Actions;

use BitCore\Application\Actions\Action;
use Modules\Backup\Requests\RestoreBackupRequest;
use Modules\Backup\Services\BackupService;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Action to restore files and/or database from a backup point.
 *
 * Endpoint: POST /api/backups/restore
 */
class RestoreBackupAction extends Action
{
    public function action(): Response
    {
        $data = RestoreBackupRequest::data();
        $errors = RestoreBackupRequest::validate($data);
        if ($errors) {
            return $this->respondWithData([
                'status' => false,
                'errors' => $errors->all(),
            ], 422);
        }
        $backupService = $this->container->get(BackupService::class);
        $result = $backupService->restoreBackup($data);
        return $this->respondWithData($result, $result['status'] ? 200 : 400);
    }
} 