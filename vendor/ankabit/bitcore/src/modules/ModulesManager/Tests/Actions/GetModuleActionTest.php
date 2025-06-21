<?php

declare(strict_types=1);

namespace BitCore\Modules\ModulesManager\Tests\Actions;

use BitCore\Modules\ModulesManager\Tests\TestCase;
use BitCore\Modules\ModulesManager\Tests\Traits\CreateTestModules;

class GetModuleActionTest extends TestCase
{
    use CreateTestModules;

    /**
     * Generate route for fethcing modules by ID.
     */
    private function getRoute(int|string $modulesId): string
    {
        return $this->generateRouteUrl('modules.show', ['name' => $modulesId]);
    }


    /**
     * Test fetching a single module successfully.
     */
    public function testGetModuleActionSuccess()
    {
        $app = $this->getAppInstance();

        // Create a sample module
        $module = $this->createModule([
            'name'        => 'Test Module',
            'priority'    => 2,
            'status'      => 'active',
            'type'        => 'custom',
            'plan'        => 'basic',
            'description' => 'A module for testing.',
            'images'      => ['/images/sample.png'],
        ]);

        // Send GET request to fetch the module by ID
        $request = $this->createRequest('GET', $this->getRoute($module->name));
        $response = $app->handle($request);

        // Decode the response
        $payload = json_decode((string) $response->getBody(), true);

        // Assertions
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($payload['data']['status']);
        $this->assertEquals(trans('ModulesManager::messages.module_fetched'), $payload['data']['message']);
        $this->assertArrayHasKey('module', $payload['data']);
        $this->assertEquals($module->id, $payload['data']['module']['id']);
    }

    /**
     * Test fetching a module that does not exist (should return 404).
     */
    public function testGetModuleActionNotFound()
    {
        $app = $this->getAppInstance();

        // Send GET request for a non-existent module ID
        $request = $this->createRequest('GET', $this->getRoute('nonexisting'));
        $response = $app->handle($request);

        // Decode the response
        $payload = json_decode((string) $response->getBody(), true);

        // Assertions
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertFalse($payload['data']['status']);
        $this->assertEquals(trans('ModulesManager::messages.module_not_found'), $payload['data']['message']);
    }
}
