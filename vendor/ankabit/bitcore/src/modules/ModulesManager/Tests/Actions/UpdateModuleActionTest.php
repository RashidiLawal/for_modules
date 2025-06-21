<?php

declare(strict_types=1);

namespace BitCore\Modules\ModulesManager\Tests\Actions;

use BitCore\Modules\ModulesManager\Tests\TestCase;
use BitCore\Modules\ModulesManager\Tests\Traits\CreateTestModules;

class UpdateModuleActionTest extends TestCase
{
    use CreateTestModules; // Trait to create dummy test modules

    /**
     * Generate route for update modules by ID.
     */
    private function getRoute(int|string $modulesId): string
    {
        return $this->generateRouteUrl('modules.update', ['name' => $modulesId]);
    }

    /**
     * Test successful update of a module.
     */
    public function testUpdateModuleActionSuccess()
    {
        $app = $this->getAppInstance();

        // Create a test module to update
        $module = $this->createModule([
            'name'        => 'Test Module',
            'description' => 'Test description',
            'status'      => 'inactive',
        ]);

        // New data for the update
        $requestData = [
            'name'        => 'Updated Module Name',
            'description' => 'Updated description for the module.',
            'status'      => 'active',
        ];

        // Make PUT request to update the module
        $request = $this->createRequestWithCsrf(
            $this->app,
            $this->getRoute($module->name),
            'PUT',
            $requestData
        );
        $response = $app->handle($request);

        $payload = json_decode((string) $response->getBody(), true);

        // Assertions
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('status', $payload['data']);
        $this->assertArrayHasKey('message', $payload['data']);
        $this->assertEquals(trans("ModulesManager::messages.module_updated"), $payload['data']['message']);
    }

    /**
     * Test update attempt on non-existing module.
     */
    public function testUpdateModuleActionNotFound()
    {
        $app = $this->getAppInstance();

        $requestData = [
            'name'        => 'Non-existent Module',
            'description' => 'No description',
            'status'      => 'inactive',
        ];

        $request = $this->createRequestWithCsrf($this->app, $this->getRoute('nonexisting'), 'PUT', $requestData);
        $response = $app->handle($request);

        $payload = json_decode((string) $response->getBody(), true);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertFalse($payload['data']['status']);
        $this->assertEquals(trans("ModulesManager::messages.module_not_found"), $payload['data']['message']);
    }
}
