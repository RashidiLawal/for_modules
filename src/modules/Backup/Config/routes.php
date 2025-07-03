<?php

/**
 * Backup Module API Routes
 *
 * Endpoints:
 *  - POST   /api/backups           : Create a new backup (files/database)
 *  - POST   /api/backups/restore   : Restore from a backup point
 *  - GET    /api/backups           : List all backups
 *  - POST   /api/backups/upload    : Upload backup to remote storage
 *  - POST   /api/backups/schedule  : Schedule/automate backups
 */
declare(strict_types=1);

use BitCore\Kernel\App;
use Modules\Backup\Actions\{
    CreateBackupAction,
    RestoreBackupAction,
    ListBackupsAction,
    UploadBackupAction,
    ScheduleBackupAction
};


return function (App $app) {
    $app->group('/api/backups', function ($group) {
        $group->post('', CreateBackupAction::class)
            ->setName('backups.create');

        $group->post('/restore', RestoreBackupAction::class)
            ->setName('backups.restore');

        $group->get('', ListBackupsAction::class)
            ->setName('backups.list');

        $group->post('/upload', UploadBackupAction::class)
            ->setName('backups.upload');

        $group->post('/schedule', ScheduleBackupAction::class)
            ->setName('backups.schedule');
    });
}; 