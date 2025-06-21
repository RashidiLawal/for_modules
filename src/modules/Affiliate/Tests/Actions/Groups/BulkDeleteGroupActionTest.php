<?php

declare(strict_types=1);

namespace Modules\Affiliate\Tests\Actions\Groups;

use Modules\Affiliate\Models\Group;
use Modules\Affiliate\Tests\TestCase;
use Modules\Affiliate\Tests\Traits\CreateTestGroups;

class BulkDeleteGroupActionTest extends TestCase
{
    use CreateTestGroups;

    /**
    * Generate route for updating a group by ID.
    */
    private function getRoute(): string
    {
        return $this->generateRouteUrl('groups.bulkDelete');
    }
    /**
     * Test successful bulk deletion of groups.
     */
    public function testBulkDeleteGroupActionSuccess(): void
    {
        $app = $this->getAppInstance();

        // Create test groups
        $group1 = $this->createGroup();
        $group2 = $this->createGroup();
        $group3 = $this->createGroup();

        $requestData = [
            'ids' => [$group1->id, $group2->id, $group3->id],
        ];

        // Make a DELETE request to bulk delete groups
        $request = $this->createRequestWithCsrf($app, $this->getRoute(), 'DELETE', $requestData);
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        // Assertions
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(200, $payload['statusCode']);
        $this->assertArrayHasKey('status', $payload['data']);
        $this->assertTrue($payload['data']['status']);
        $this->assertEquals(trans("Affiliate::messages.bulk_group_delete_success"), $payload['data']['message']);
    }

    /**
     * Test bulk deletion with invalid or missing IDs.
     */
    public function testBulkDeleteGroupValidationError(): void
    {
        $app = $this->getAppInstance();

        $requestData = [
            'id' => [], // Empty IDs array
        ];

        // Make a DELETE request with invalid data
        $request = $this->createRequestWithCsrf($app, $this->getRoute(), 'DELETE', $requestData);
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        // Assertions
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals(422, $payload['statusCode']);
        $this->assertArrayHasKey('status', $payload['data']);
        $this->assertFalse($payload['data']['status']);
        $this->assertEquals(trans("Affiliate::messages.invalid_id_list"), $payload['data']['message']);
    }
}
