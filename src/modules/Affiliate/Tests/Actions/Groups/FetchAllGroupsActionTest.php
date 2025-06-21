<?php

declare(strict_types=1);

namespace Modules\Affiliate\Tests\Actions\Groups;

use Modules\Affiliate\Models\Group;
use Modules\Affiliate\Tests\TestCase;
use Modules\Affiliate\Tests\Traits\CreateTestGroups;

class FetchAllGroupsActionTest extends TestCase
{
    use CreateTestGroups;

    /**
     * Generate route for fetching groups with optional query parameters.
     *
     * @param array $queryParams Associative array of query parameters.
     * @return string The generated route URL.
     */
    private function getRoute(array $queryParams = []): string
    {
        return $this->generateRouteUrl('groups.index', [], $queryParams);
    }

    /**
     * Test successful fetching of all groups with pagination.
     */
    public function testFetchAllGroupsActionSuccess(): void
    {
        $app = $this->getAppInstance();

        // Create test groups
        $this->createGroup(['group_name' => 'Group 1']);
        $this->createGroup(['group_name' => 'Group 2']);
        $this->createGroup(['group_name' => 'Group 3']);

        // Make a GET request to fetch all groups
        $request = $this->createRequest('GET', $this->getRoute([
            'page' => 1,
            'per_page' => 2
        ]));
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        // Assertions
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(200, $payload['statusCode']);
        $this->assertTrue($payload['data']['status']);
        $this->assertArrayHasKey('groups', $payload['data']);
    }

    /**
     * Test fetching groups with filters.
     */
    public function testFetchAllGroupsActionWithFilters(): void
    {
        $app = $this->getAppInstance();

        // Create test groups
        $this->createGroup(['group_name' => 'Group A']);
        $this->createGroup(['group_name' => 'Group B']);
        $this->createGroup(['group_name' => 'Group C']);

        // Make a GET request with a filter
        $request = $this->createGetRequest($this->getRoute(), ['filters' => ['group_name' => 'Group A']]);
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        // Assertions
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(200, $payload['statusCode']);
        $this->assertTrue($payload['data']['status']);
        $this->assertArrayHasKey('groups', $payload['data']);
    }

    /**
     * Test fetching groups with sorting.
     */
    public function testFetchAllGroupsActionWithSorting(): void
    {
        $app = $this->getAppInstance();

        // Create test groups
        $this->createGroup(['group_name' => 'Group Z']);
        $this->createGroup(['group_name' => 'Group A']);
        $this->createGroup(['group_name' => 'Group M']);

        // Make a GET request with sorting
        $request = $this->createGetRequest($this->getRoute(), [
            'sort_by' => 'group_name',
            'sort_order' => 'ASC'
        ]);
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        // Assertions
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(200, $payload['statusCode']);
        $this->assertTrue($payload['data']['status']);
        $this->assertArrayHasKey('groups', $payload['data']);
    }
}
