<?php

declare(strict_types=1);

namespace Modules\Backup\Requests;

use BitCore\Application\Services\Requests\RequestValidator;

/**
 * Request validation for restoring a backup.
 */
class RestoreBackupRequest extends RequestValidator
{
    /**
     * Get the validation rules for restoring a backup.
     *
     * @return array
     */
    public static function rules(): array
    {
        return [
            'backup_id' => 'required|integer|exists:backups,id',
            'type' => 'required|string|in:files,database,both',
        ];
    }
} 