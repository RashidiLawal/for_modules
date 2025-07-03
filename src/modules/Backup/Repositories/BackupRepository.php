<?php

declare(strict_types=1);

namespace Modules\Backup\Repositories;

use BitCore\Application\Repositories\AppRepository;
use Modules\Backup\Models\Backup;

/**
 * Class BackupRepository
 *
 * Implements data access methods for backup records.
 */
class BackupRepository extends AppRepository implements BackupRepositoryInterface
{
    /**
     * The model class for this repository.
     *
     * @var string
     */
    protected string $modelClass = Backup::class;
    // Additional backup-specific methods can be added here if needed.
}
