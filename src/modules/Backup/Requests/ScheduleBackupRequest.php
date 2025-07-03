<?php

declare(strict_types=1);

namespace Modules\Backup\Requests;

use BitCore\Application\Services\Requests\RequestValidator;

/**
 * Request validation for scheduling/automating backups.
 */
class ScheduleBackupRequest extends RequestValidator
{
    /**
     * Get the validation rules for scheduling a backup.
     *
     * @return array
     */
    public static function rules(): array
    {
        return [
            'interval' => 'required|string|in:hourly,daily,weekly,monthly',
            'time' => 'string', // e.g., '02:00' for daily
            'type' => 'required|string|in:files,database,both',
            'paths' => 'array',
            'disk' => 'string',
        ];
    }
} 