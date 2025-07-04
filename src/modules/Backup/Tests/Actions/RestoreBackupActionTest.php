<?php

declare(strict_types=1);

namespace Modules\Backup\Tests\Actions;

use Modules\Backup\Tests\TestCase;

/**
 * Test restoring a backup.
 */
class RestoreBackupActionTest extends TestCase
{
    /**
     * Test restoring a backup (files).
     */
    public function testRestoreBackup(): void
    {
        $app = $this->getAppInstance();

        // Create a backup record
        $backup = $this->backupRepository->create([
            'type' => 'files',
            'path' => 'backups/test.zip',
            'disk' => 'local',
            'status' => 'completed',
        ]);

        $requestData = [
            'backup_id' => $backup->id,
            'type' => 'files'
        ];

        $request = $this->createRequestWithCsrf($app, '/api/backups/restore', 'POST', $requestData);
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($payload['status']);
    }
} 