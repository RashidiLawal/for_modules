<?php

declare(strict_types=1);

namespace BitCore\Modules\ModulesManager\Tests\Actions;

use BitCore\Modules\ModulesManager\Tests\TestCase;
use BitCore\Modules\ModulesManager\Tests\Traits\CreateTestModules;

class DeactivateModuleActionTest extends TestCase
{
    use CreateTestModules;

    /**
     * Generate route for deactivate modules by ID.
     */
    private function getRoute(int|string $modulesId): string
    {
        return $this->generateRouteUrl('modules.deactivate', ['name' => $modulesId]);
    }

    /**
     * Test successful deactivation of a module.
     */
    public function testDeactivateModuleSuccess()
    {
        $app = $this->getAppInstance();

        // Create a module with 'active' status
        $module = $this->createModule([
            'name'        => 'TestModuleToDeactivate',
            'status'      => 'active',
            'type'        => 'custom',
            'plan'        => 'basic',
            'description' => 'Module to be deactivated',
        ]);

        // Send POST request to deactivate the module
        $request = $this->createRequestWithCsrf(
            $this->app,
            $this->getRoute($module->name),
            'POST'
        );
        $response = $app->handle($request);

        // Decode the response
        $payload = json_decode((string) $response->getBody(), true);

        // Assertions
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($payload['data']['status']);
        $this->assertEquals(trans('ModulesManager::messages.module_deactivated'), $payload['data']['message']);
    }

    /**
     * Test deactivation attempt of a non-existent module (should return 404).
     */
    public function testDeactivateModuleNotFound()
    {
        $app = $this->getAppInstance();

        // Send POST request for a non-existent module name
        $request = $this->createRequestWithCsrf(
            $this->app,
            $this->getRoute('nonexisting'),
            'POST'
        );
        $response = $app->handle($request);

        // Decode the response
        $payload = json_decode((string) $response->getBody(), true);

        // Assertions
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertFalse($payload['data']['status']);
        $this->assertEquals(trans('ModulesManager::messages.module_not_found'), $payload['data']['message']);
    }
}
