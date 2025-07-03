<?php

declare(strict_types=1);

namespace Modules\Backup\Models;

use BitCore\Application\Models\AppModel;

/**
 * BackupSchedule Model
 *
 * Stores recurring backup schedule info.
 *
 * @property string $interval   Interval (hourly, daily, weekly, monthly)
 * @property string $time       Time of day (e.g., '02:00')
 * @property string $type       Type of backup (files, database, both)
 * @property array  $paths      Files/directories to backup
 * @property string $disk       Storage disk
 * @property string $status     Status (active, paused)
 * @property string $last_run_at Last run timestamp
 * @property string $next_run_at Next run timestamp
 */
class BackupSchedule extends AppModel
{
    protected $table = 'backup_schedules';
    protected $fillable = [
        'interval',
        'time',
        'type',
        'paths',
        'disk',
        'status',
        'last_run_at',
        'next_run_at',
    ];
    protected $casts = [
        'paths' => 'array',
        'last_run_at' => 'datetime',
        'next_run_at' => 'datetime',
    ];
} 