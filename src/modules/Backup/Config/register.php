<?php

/**
 * Backup Module Service Registration
 *
 * Registers the BackupRepository for dependency injection.
 */
use BitCore\Foundation\Container;
use Modules\Backup\Repositories\BackupRepository;
use Modules\Backup\Repositories\BackupRepositoryInterface;

return function (Container $container) {
    $container->singleton(BackupRepositoryInterface::class, BackupRepository::class);
}; 