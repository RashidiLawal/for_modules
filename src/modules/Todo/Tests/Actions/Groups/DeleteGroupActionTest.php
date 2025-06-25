<?php

declare(strict_types=1);

namespace Modules\Todo\Tests\Actions\Groups;

use Modules\Todo\Models\Group;
use Modules\Todo\Tests\TestCase;
use Modules\Todo\Tests\Traits\CreateTestGroups;

class DeleteGroupActionTest extends TestCase
{
    use CreateTestGroups;

     /**
     * Generate route for updating a group by ID.
     */
    private function getRoute(int|string $groupId): string
    {
        return $this->generateRouteUrl('groups.delete', ['id' => $groupId]);
    }

    /**
     * Test successful deletion of a group.
     */
    public function testDeleteGroupActionSuccess(): void
    {
        $app = $this->getAppInstance();

        // Create a test group
        $group = $this->createGroup();

        // Make a DELETE request to delete the group
        $request = $this->createRequestWithCsrf(
            $app,
            $this->getRoute($group->id),
            'DELETE'
        );
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        // Assertions
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(200, $payload['statusCode']);
        $this->assertTrue($payload['data']['status']);
        $this->assertEquals(trans("Todo::messages.group_deleted"), $payload['data']['message']);
    }

    /**
     * Test deletion of a non-existing group.
     */
    public function testDeleteGroupActionNotFound(): void
    {
        $app = $this->getAppInstance();

        // Make a DELETE request for a non-existing group ID
        $request = $this->createRequestWithCsrf(
            $app,
            $this->getRoute(9999), // Non-existing ID
            'DELETE'
        );
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        // Assertions
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals(404, $payload['statusCode']);
        $this->assertFalse($payload['data']['status']);
        $this->assertEquals(trans("Todo::messages.group_not_found"), $payload['data']['message']);
    }
}
