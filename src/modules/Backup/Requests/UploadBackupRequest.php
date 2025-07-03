<?php

declare(strict_types=1);

namespace Modules\Backup\Requests;

use BitCore\Application\Services\Requests\RequestValidator;

/**
 * Request validation for uploading a backup to remote storage.
 */
class UploadBackupRequest extends RequestValidator
{
    /**
     * Get the validation rules for uploading a backup.
     *
     * @return array
     */
    public static function rules(): array
    {
        return [
            'backup_id' => 'required|integer|exists:backups,id',
            'provider' => 'required|string|in:s3,google,onedrive,wasabi,ssh,ftp',
            'credentials' => 'array',
        ];
    }
} 