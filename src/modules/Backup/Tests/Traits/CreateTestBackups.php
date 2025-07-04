<?php

declare(strict_types=1);

namespace Modules\Backup\Tests\Traits;

use Modules\Backup\Models\Backup;

/**
 * Trait CreateTestBackups
 *
 * Provides helper methods for creating test backup records in Backup module tests.
 */
trait CreateTestBackups
{
    /**
     * Create a test backup record.
     *
     * @param array $overrides
     * @return Backup
     */
    protected function createBackup(array $overrides = []): Backup
    {
        $defaults = [
            'type' => 'files',
            'path' => 'backups/test_' . uniqid() . '.zip',
            'disk' => 'local',
            'status' => 'completed',
        ];
        return Backup::create(array_merge($defaults, $overrides));
    }
} 