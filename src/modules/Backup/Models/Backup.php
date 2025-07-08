<?php

declare(strict_types=1);

namespace Modules\Backup\Models;

use BitCore\Foundation\Database\Eloquent\SoftDeletes;
use BitCore\Application\Models\AppModel;

/**
 * Backup Model
 *
 * Stores metadata for each backup (files, database, or both).
 *
 * @property string $type         Type of backup (files, database, both)
 * @property string $path         Path to the backup file
 * @property string $disk         Storage disk used
 * @property string $status       Status of the backup
 * @property string $scheduled_at Scheduled time for backup
 * @property string $completed_at Completion time for backup
 * @property string $uploaded_to  Remote storage location
 * @property array  $meta         Additional metadata (JSON)
 */
class Backup extends AppModel
{
    use SoftDeletes;
    protected $table = 'backups';
    protected $fillable = [
        'type', // files, database, both
        'file_path',
        'disk',
        'status',
        'scheduled_at',
        'completed_at',
        'uploaded_to',
        'meta', // json
    ];
    protected $casts = [
        'meta' => 'array',
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];
} 