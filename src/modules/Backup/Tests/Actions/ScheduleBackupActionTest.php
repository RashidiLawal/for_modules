<?php

declare(strict_types=1);

namespace Modules\Backup\Tests\Actions;

use Modules\Backup\Tests\TestCase;

/**
 * Test scheduling a backup.
 */
class ScheduleBackupActionTest extends TestCase
{
    /**
     * Test scheduling a backup (daily).
     */
    public function testScheduleBackup(): void
    {
        $app = $this->getAppInstance();

        $requestData = [
            'interval' => 'daily',
            'time' => '02:00',
            'type' => 'files',
            'paths' => [__DIR__ . '/dummy.txt'],
            'disk' => 'local'
        ];

        $request = $this->createRequestWithCsrf($app, '/api/backups/schedule', 'POST', $requestData);
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($payload['status']);
        $this->assertEquals('Backup schedule set successfully', $payload['message']);
    }
} 