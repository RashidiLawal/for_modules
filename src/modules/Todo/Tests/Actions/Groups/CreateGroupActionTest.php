<?php

declare(strict_types=1);

namespace Modules\Todo\Tests\Actions\Groups;

use Modules\Todo\Tests\TestCase;
use Modules\Todo\Tests\Traits\CreateTestGroups;

class CreateGroupActionTest extends TestCase
{
    use CreateTestGroups;

    /**
    * Generate route for updating a contract by ID.
    */
    private function getRoute(): string
    {
        return $this->generateRouteUrl('groups.store');
    }

    /**
     * Test successful creation of a group.
     */
    public function testCreateGroupActionSuccess(): void
    {
        $app = $this->getAppInstance();

        $requestData = [
            'group_title'               => 'Elite Group ' . uniqid(),
            'group_description'        => 'Elite Group Description ' . uniqid(),
            'completed'                => true,
            'group_slug'               => 'elite-group-' . uniqid()
        ];

        $request = $this->createRequestWithCsrf($app, $this->getRoute(), 'POST', $requestData);
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        // Assertions
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals(201, $payload['statusCode']);
        $this->assertArrayHasKey('status', $payload['data']);
        $this->assertTrue($payload['data']['status']);
        $this->assertEquals(trans("Todo::messages.group_created"), $payload['data']['message']);
        $this->assertArrayHasKey('data', $payload['data']);
        $this->assertNotEmpty($payload['data']['data']);
    }

    /**
     * Test creation of a group with missing required fields.
     */
    public function testCreateGroupValidationError(): void
    {
        $app = $this->getAppInstance();

        $requestData = [
            // Missing required fields like group_name, group_slug, and default_commission_rate
        ];

        $request = $this->createRequestWithCsrf($app, $this->getRoute(), 'POST', $requestData);
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        // Assertions
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals(422, $payload['statusCode']);
        $this->assertArrayHasKey('status', $payload['data']);
        $this->assertFalse($payload['data']['status']);
        $this->assertArrayHasKey('errors', $payload['data']);
        $this->assertNotEmpty($payload['data']['errors']);
    }
}
