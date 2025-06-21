<?php

declare(strict_types=1);

namespace BitCore\Modules\ModulesManager\Tests\Actions;

use BitCore\Modules\ModulesManager\Tests\TestCase;
use BitCore\Modules\ModulesManager\Tests\Traits\CreateTestModules;

class DeleteModuleActionTest extends TestCase
{
    use CreateTestModules; // Trait for creating test modules

    /**
     * Generate route for update modules by ID.
     */
    private function getRoute(int|string $modulesId): string
    {
        return $this->generateRouteUrl('modules.delete', ['name' => $modulesId]);
    }

    /**
     * Test successful deletion of a module.
     */
    public function testDeleteModuleActionSuccess()
    {
        $app = $this->getAppInstance();

        // Create a test module to delete
        $module = $this->createModule([
            'name'        => 'Blog',
            'namespace'   => 'Modules\\Blog',
            'description' => 'A blogging module',
            'version'     => '1.0.0',
        ]);

        // Make a DELETE request to remove the module
        $request = $this->createRequestWithCsrf($this->app, $this->getRoute($module->name), 'DELETE');
        $response = $app->handle($request);

        // Parse the response payload
        $payload = json_decode((string) $response->getBody(), true);

        // Assertions
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($payload['data']['status']);
        $this->assertEquals(trans("ModulesManager::messages.module_deleted"), $payload['data']['message']);
    }

    /**
     * Test deletion of a non-existent module.
     */
    public function testDeleteModuleActionNotFound()
    {
        $app = $this->getAppInstance();

        // Try to delete a non-existent module
        $request = $this->createRequestWithCsrf($this->app, $this->getRoute('nonexisting'), 'DELETE');
        $response = $app->handle($request);

        // Parse the response payload
        $payload = json_decode((string) $response->getBody(), true);

        // Assertions
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertFalse($payload['data']['status']);
        $this->assertEquals(trans("ModulesManager::messages.module_not_found"), $payload['data']['message']);
    }
}
