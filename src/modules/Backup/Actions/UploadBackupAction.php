<?php

declare(strict_types=1);

namespace Modules\Backup\Actions;

use BitCore\Application\Actions\Action;
use Modules\Backup\Requests\UploadBackupRequest;
use Modules\Backup\Services\RemoteUploadService;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Action to upload a backup to remote storage (S3, Google Drive, etc.).
 *
 * Endpoint: POST /api/backups/upload
 */
class UploadBackupAction extends Action
{
    public function action(): Response
    {
        $data = UploadBackupRequest::data();
        $errors = UploadBackupRequest::validate($data);
        if ($errors) {
            return $this->respondWithData([
                'status' => false,
                'errors' => $errors->all(),
            ], 422);
        }
        $uploadService = $this->container->get(RemoteUploadService::class);
        $result = $uploadService->upload($data);
        return $this->respondWithData($result, $result['status'] ? 200 : 400);
    }
} 