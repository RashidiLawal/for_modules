<?php

declare(strict_types=1);

namespace Modules\Affiliate\Tests\Actions\Groups;

use Modules\Affiliate\Models\Group;
use Modules\Affiliate\Tests\TestCase;
use Modules\Affiliate\Tests\Traits\CreateTestGroups;

class EditGroupActionTest extends TestCase
{
    use CreateTestGroups;

      /**
     * Generate route for updating a group by ID.
     */
    private function getRoute(int|string $groupId): string
    {
        return $this->generateRouteUrl('groups.update', ['id' => $groupId]);
    }

    /**
     * Test successful editing of a group.
     */
    public function testEditGroupActionSuccess(): void
    {
        $app = $this->getAppInstance();

        // Create a test group
        $group = $this->createGroup();

        $requestData = [
            'group_name'               => 'Test Group ' . uniqid(),
            'group_slug'               => 'test-group-' . uniqid(),
        ];

        // Make a PUT request to edit the group
        $request = $this->createRequestWithCsrf(
            $app,
            $this->getRoute($group->id),
            'PUT',
            $requestData
        );

        // Handle the request
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        // Assertions
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(200, $payload['statusCode']);
        $this->assertTrue($payload['data']['status']);
        $this->assertEquals(trans("Affiliate::messages.group_updated"), $payload['data']['message']);
        $this->assertArrayHasKey('data', $payload['data']);
    }

    /**
     * Test editing a group with invalid data.
     */
    public function testEditGroupActionValidationError(): void
    {
        $app = $this->getAppInstance();

        // Create a test group
        $group = $this->createGroup();

        $requestData = [
            'name' => '', // Invalid name (empty)
        ];

        // Make a PUT request with invalid data
        $request = $this->createRequestWithCsrf(
            $app,
            $this->getRoute($group->id),
            'PUT',
            $requestData
        );

        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        // Assertions
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals(422, $payload['statusCode']);
        $this->assertFalse($payload['data']['status']);
        $this->assertArrayHasKey('errors', $payload['data']);
        $this->assertNotEmpty($payload['data']['errors']);
    }

    /**
     * Test editing a non-existing group.
     */
    public function testEditGroupActionNotFound(): void
    {
        $app = $this->getAppInstance();

        $requestData = [
            'group_name'               => 'Test Group ' . uniqid(),
            'group_slug'               => 'test-group-' . uniqid(),
            'clicks_generated'         => 0,
        ];

        // Make a PUT request for a non-existing group ID
        $request = $this->createRequestWithCsrf(
            $app,
            $this->getRoute(99999), // Non-existing ID
            'PUT',
            $requestData
        );

        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        // Assertions
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals(404, $payload['statusCode']);
        $this->assertFalse($payload['data']['status']);
        $this->assertEquals(trans("Affiliate::messages.group_not_found"), $payload['data']['message']);
    }
}
