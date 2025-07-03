<?php

declare(strict_types=1);

namespace Modules\Backup\Requests;

use BitCore\Application\Services\Requests\RequestValidator;

/**
 * Request validation for creating a backup.
 */
class CreateBackupRequest extends RequestValidator
{
    /**
     * Get the validation rules for creating a backup.
     *
     * @return array
     */
    public static function rules(): array
    {
        return [
            'type' => 'required|string|in:files,database,both',
            'paths' => 'array', // for files backup
            'disk' => 'string', // storage disk
            'database_connection' => 'string', // for database backup
        ];
    }
} 