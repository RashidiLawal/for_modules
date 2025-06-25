<?php

declare(strict_types=1);

namespace Modules\Todo\Tests\Actions\Groups;

use Modules\Todo\Models\Group;
use Modules\Todo\Tests\TestCase;
use Modules\Todo\Tests\Traits\CreateTestGroups;

class GetSingleGroupActionTest extends TestCase
{
    use CreateTestGroups;

     /**
     * Generate route for updating a group by ID.
     */
    private function getRoute(int|string $groupId): string
    {
        return $this->generateRouteUrl('groups.show', ['id' => $groupId]);
    }

    /**
     * Test successful fetching of a single group.
     */
    public function testGetSingleGroupActionSuccess(): void
    {
        $app = $this->getAppInstance();

        // Create a test group
        $group = $this->createGroup();

        // Make a GET request to fetch the group
        $request = $this->createRequest('GET', $this->getRoute($group->id));
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        // Assertions
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(200, $payload['statusCode']);
        $this->assertTrue($payload['data']['status']);
        $this->assertEquals(trans("Todo::messages.group_fetched"), $payload['data']['message']);
    }

    /**
     * Test fetching a non-existing group.
     */
    public function testGetSingleGroupActionNotFound(): void
    {
        $app = $this->getAppInstance();

        // Make a GET request for a non-existing group ID
        $request = $this->createRequest('GET', $this->getRoute(99999)); // Non-existing ID
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        // Assertions
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals(404, $payload['statusCode']);
        $this->assertFalse($payload['data']['status']);
        $this->assertEquals(trans("Todo::messages.group_not_found"), $payload['data']['message']);
    }
}
