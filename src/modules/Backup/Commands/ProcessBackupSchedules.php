<?php

declare(strict_types=1);

// Usage: php src/modules/Backup/Commands/ProcessBackupSchedules.php
// Add to cron: * * * * * php /path/to/src/modules/Backup/Commands/ProcessBackupSchedules.php

require_once __DIR__ . '/../../../bootstrap.php';

use Modules\Backup\Models\BackupSchedule;
use Modules\Backup\Services\BackupService;

/**
 * CLI Worker: Process and run due backup schedules.
 *
 * - Finds all active schedules.
 * - Checks if each is due to run.
 * - Triggers backup and updates schedule timestamps.
 * - Logs output to stdout.
 */

// Get DI container
$container = container();
/** @var BackupService $backupService */
$backupService = $container->get(BackupService::class);

// Get current time
$now = new DateTimeImmutable();

// Fetch all active schedules
$schedules = BackupSchedule::where('status', 'active')->get();

foreach ($schedules as $schedule) {
    // Determine if schedule is due
    $isDue = isScheduleDue($schedule, $now);
    if (!$isDue) {
        continue;
    }
    // Prepare backup data
    $data = [
        'type' => $schedule->type,
        'paths' => $schedule->paths,
        'disk' => $schedule->disk,
    ];
    echo "[{$now->format('Y-m-d H:i:s')}] Running backup for schedule #{$schedule->id} ({$schedule->interval})... ";
    $result = $backupService->createBackup($data);
    if ($result['status']) {
        echo "SUCCESS\n";
        $schedule->last_run_at = $now;
        $schedule->next_run_at = computeNextRunAt($schedule, $now);
        $schedule->save();
    } else {
        echo "FAILED: {$result['message']}\n";
    }
}

/**
 * Determine if a schedule is due to run.
 * @param BackupSchedule $schedule
 * @param DateTimeImmutable $now
 * @return bool
 */
function isScheduleDue(BackupSchedule $schedule, DateTimeImmutable $now): bool
{
    if (!$schedule->next_run_at) {
        // Never run before, so it's due
        return true;
    }
    $next = $schedule->next_run_at instanceof DateTimeInterface
        ? $schedule->next_run_at
        : new DateTimeImmutable($schedule->next_run_at);
    return $now >= $next;
}

/**
 * Compute the next run time for a schedule.
 * @param BackupSchedule $schedule
 * @param DateTimeImmutable $from
 * @return DateTimeImmutable
 */
function computeNextRunAt(BackupSchedule $schedule, DateTimeImmutable $from): DateTimeImmutable
{
    switch ($schedule->interval) {
        case 'hourly':
            return $from->modify('+1 hour');
        case 'daily':
            $time = $schedule->time ?? '00:00';
            $next = DateTimeImmutable::createFromFormat('Y-m-d H:i', $from->format('Y-m-d') . ' ' . $time);
            if ($from >= $next) {
                $next = $next->modify('+1 day');
            }
            return $next;
        case 'weekly':
            $time = $schedule->time ?? '00:00';
            $next = DateTimeImmutable::createFromFormat('Y-m-d H:i', $from->format('Y-m-d') . ' ' . $time);
            if ($from >= $next) {
                $next = $next->modify('+7 days');
            }
            return $next;
        case 'monthly':
            $time = $schedule->time ?? '00:00';
            $next = DateTimeImmutable::createFromFormat('Y-m-d H:i', $from->format('Y-m-d') . ' ' . $time);
            if ($from >= $next) {
                $next = $next->modify('+1 month');
            }
            return $next;
        default:
            return $from->modify('+1 day');
    }
} 