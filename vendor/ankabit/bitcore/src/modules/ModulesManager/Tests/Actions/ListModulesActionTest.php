<?php

declare(strict_types=1);

namespace BitCore\Modules\ModulesManager\Tests\Actions;

use BitCore\Modules\ModulesManager\Tests\TestCase;
use BitCore\Modules\ModulesManager\Tests\Traits\CreateTestModules;

class ListModulesActionTest extends TestCase
{
    use CreateTestModules;

    /**
     * Generate route for fetching leads with optional query parameters.
     *
     * @param array $queryParams Associative array of query parameters.
     * @return string The generated route URL.
     */
    private function getRoute(array $queryParams = []): string
    {
        return $this->generateRouteUrl('modules.index', [], $queryParams);
    }

    /**
     * Test fetching all modules successfully with no filters.
     */
    public function testListModulesActionSuccess()
    {
        $app = $this->getAppInstance();

        // Create sample module in DB
        $this->createModule([
            'name'        => 'Custom Analytics',
            'priority'    => 1,
            'status'      => 'active',
            'type'        => 'custom',
            'plan'        => 'free',
            'description' => 'A test custom module',
            'images'      => ['/path/to/image.jpg']
        ]);

        // Send GET request to list all modules
        $request = $this->createRequest('GET', $this->getRoute());
        $response = $app->handle($request);

        // Decode response
        $payload = json_decode((string) $response->getBody(), true);

        // Assertions
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($payload['data']['status']);
        $this->assertEquals(trans('ModulesManager::messages.modules_fetched'), $payload['data']['message']);
        $this->assertIsArray($payload['data']['modules']);
        $this->assertGreaterThan(0, count($payload['data']['modules']));
    }

    /**
     * Test fetching modules with a filter.
     */
    public function testListModulesActionWithFilters()
    {
        $app = $this->getAppInstance();

        // Create a module that matches the filter
        $this->createModule([
            'name'        => 'Filtered Module',
            'priority'    => 3,
            'status'      => 'active',
            'type'        => 'custom',
            'plan'        => 'premium',
            'description' => 'For filter testing',
            'images'      => []
        ]);

        // Add a query filter
        $request = $this->createGetRequest(
            $this->getRoute(),
            ['filters' => ['type' => 'custom']]
        );
        $response = $app->handle($request);

        $payload = json_decode((string) $response->getBody(), true);

        // Assertions
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($payload['data']['status']);
        $this->assertEquals(trans('ModulesManager::messages.modules_fetched'), $payload['data']['message']);
        $this->assertIsArray($payload['data']['modules']);
        $this->assertGreaterThan(0, count($payload['data']['modules']));
    }
}
