<?php

declare(strict_types=1);

namespace Modules\Backup\Tests\Actions;

use Modules\Backup\Tests\TestCase;

/**
 * Test the creation of a backup (files).
 */
class CreateBackupActionTest extends TestCase
{
    /**
     * Test successful creation of a backup (files).
     */
    public function testCreateBackupSuccess(): void
    {
        $app = $this->getAppInstance();

        $requestData = [
            'type' => 'files',
            'paths' => [__DIR__ . '/dummy.txt'], // Use dummy file for backup
            'disk' => 'local'
        ];

        $request = $this->createRequestWithCsrf($app, '/api/backups', 'POST', $requestData);
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertTrue($payload['status']);
        $this->assertEquals('Backup created successfully', $payload['message']);
        $this->assertArrayHasKey('backup', $payload);
    }
} 