<?php

declare(strict_types=1);

namespace Modules\Backup\Actions;

use BitCore\Application\Actions\Action;
use Modules\Backup\Requests\ScheduleBackupRequest;
use Modules\Backup\Services\ScheduleService;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Action to schedule/automate backups.
 *
 * Endpoint: POST /api/backups/schedule
 */
class ScheduleBackupAction extends Action
{
    public function action(): Response
    {
        $data = ScheduleBackupRequest::data();
        $errors = ScheduleBackupRequest::validate($data);
        if ($errors) {
            return $this->respondWithData([
                'status' => false,
                'errors' => $errors->all(),
            ], 422);
        }
        $scheduleService = $this->container->get(ScheduleService::class);
        $result = $scheduleService->schedule($data);
        return $this->respondWithData($result, $result['status'] ? 200 : 400);
    }
} 