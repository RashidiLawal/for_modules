<?php

namespace BitCore\Modules\ModulesManager\Models;

use BitCore\Application\Models\AppModel;

/**
 * Class Module
 *
 * Represents a module that has been uploaded to the system.
 *
 * The following are the available columns in the "modules_manager" table:
 *
 * @property int $id
 * @property string $name
 * @property string|null $status
 * @property string|null $type
 * @property array|null $metadata
 * @property \BitCore\Foundation\Carbon|null $created_at
 * @property \BitCore\Foundation\Carbon|null $updated_at
 */
class Module extends AppModel
{
    protected $table = 'modules_manager';

    public static const STATUS_ACTIVE = 'active';
    public static const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'name',
        'status',
        'type',
        'metadata',
    ];

    protected $casts = [
        'metadata'   => 'array',
    ];
}
