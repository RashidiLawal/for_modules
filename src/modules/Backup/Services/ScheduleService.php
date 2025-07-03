<?php

declare(strict_types=1);

namespace Modules\Backup\Services;

use Modules\Backup\Models\BackupSchedule;
use Exception;

/**
 * Service for automating/scheduling backups.
 *
 * - schedule: Schedules backups at specified intervals.
 *
 * Note: Integrate with cron or external scheduler as needed.
 */
class ScheduleService
{
    /**
     * Schedule/automate backups.
     *
     * @param array $data
     * @return array
     */
    public function schedule(array $data): array
    {
        try {
            $schedule = BackupSchedule::create([
                'interval' => $data['interval'],
                'time' => $data['time'] ?? null,
                'type' => $data['type'],
                'paths' => $data['paths'] ?? [],
                'disk' => $data['disk'] ?? 'local',
                'status' => 'active',
                'last_run_at' => null,
                'next_run_at' => null,
            ]);
            return [
                'status' => true,
                'message' => 'Backup schedule set successfully',
                'schedule' => $schedule,
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to set schedule: ' . $e->getMessage(),
            ];
        }
    }
}
 