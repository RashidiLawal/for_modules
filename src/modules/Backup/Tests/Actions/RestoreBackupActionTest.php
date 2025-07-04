<?php

declare(strict_types=1);

namespace Modules\Backup\Tests\Actions;

use Modules\Backup\Tests\TestCase;
use Modules\Backup\Tests\Traits\CreateTestBackups;

/**
 * Test restoring a backup.
 */
class RestoreBackupActionTest extends TestCase
{
    use CreateTestBackups;
    /**
     * Test restoring a backup (files).
     */
    public function testRestoreBackup(): void
    {
        $app = $this->getAppInstance();

        // Create a backup record using the trait
        $backup = $this->createBackup();

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