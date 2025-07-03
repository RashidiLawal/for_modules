<?php

declare(strict_types=1);

namespace Modules\Backup\Actions;

use BitCore\Application\Actions\Action;
use Modules\Backup\Requests\CreateBackupRequest;
use Modules\Backup\Services\BackupService;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Action to create a new backup (files and/or database).
 *
 * Endpoint: POST /api/backups
 */
class CreateBackupAction extends Action
{
    public function action(): Response
    {
        // Validate request
        $data = CreateBackupRequest::data();
        $errors = CreateBackupRequest::validate($data);
        if ($errors) {
            return $this->respondWithData([
                'status' => false,
                'errors' => $errors->all(),
            ], 422);
        }
        // Call backup service
        $backupService = $this->container->get(BackupService::class);
        $result = $backupService->createBackup($data);
        return $this->respondWithData($result, $result['status'] ? 201 : 400);
    }
} 