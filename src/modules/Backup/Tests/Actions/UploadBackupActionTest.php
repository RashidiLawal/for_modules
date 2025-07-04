<?php

declare(strict_types=1);

namespace Modules\Backup\Tests\Actions;

use Modules\Backup\Tests\TestCase;

/**
 * Test uploading a backup to remote storage (Google Drive).
 */
class UploadBackupActionTest extends TestCase
{
    /**
     * Test uploading a backup to Google Drive.
     */
    public function testUploadBackupToGDrive(): void
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
            'provider' => 'gdrive'
        ];

        $request = $this->createRequestWithCsrf($app, '/api/backups/upload', 'POST', $requestData);
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertIsArray($payload);
    }
} 