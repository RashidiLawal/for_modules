<?php

declare(strict_types=1);

namespace Modules\Backup\Repositories;

use BitCore\Application\Repositories\AppRepositoryInterface;

/**
 * Interface BackupRepositoryInterface
 *
 * Extends BitCore's AppRepositoryInterface for backup records.
 * Use findById($id) and fetchWithFilters() for queries.
 */
interface BackupRepositoryInterface extends AppRepositoryInterface
{
    // Add backup-specific repository methods here if needed
} 