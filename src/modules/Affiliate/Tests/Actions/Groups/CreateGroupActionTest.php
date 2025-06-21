<?php

declare(strict_types=1);

namespace Modules\Affiliate\Tests\Actions\Groups;

use Modules\Affiliate\Tests\Traits\CreateTestGroups;
use Modules\Affiliate\Tests\TestCase;

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
            'group_name'               => 'Elite Group ' . uniqid(),
            'group_slug'               => 'elite-group-' . uniqid(),
            'clicks_generated'         => 0,
            'total_earnings'           => 0.00,
            'is_auto_approved'         => true,
            'default_commission_rate'  => 10.00,

        ];

        $request = $this->createRequestWithCsrf($app, $this->getRoute(), 'POST', $requestData);
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        // Assertions
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals(201, $payload['statusCode']);
        $this->assertArrayHasKey('status', $payload['data']);
        $this->assertTrue($payload['data']['status']);
        $this->assertEquals(trans("Affiliate::messages.group_created"), $payload['data']['message']);
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
