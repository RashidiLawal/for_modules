<?php

declare(strict_types=1);

namespace BitCore\Modules\ModulesManager\Tests\Actions;

use BitCore\Modules\ModulesManager\Tests\TestCase;
use BitCore\Modules\ModulesManager\Tests\Traits\CreateTestModules;

class ActivateModuleActionTest extends TestCase
{
    use CreateTestModules;

    /**
     * Generate route for fetch single module by ID.
     */
    private function getRoute(int|string $moduleId): string
    {
        return $this->generateRouteUrl('modules.activate', ['name' => $moduleId]);
    }

    /**
     * Test successful activation of a module.
     */
    public function testActivateModuleSuccess()
    {
        $app = $this->getAppInstance();

        // Create a module with 'inactive' status
        $module = $this->createModule([
            'name'        => 'testModule',
            'namespace'   => 'Modules\\testModule',
            'description' => 'A blogging module',
            'version'     => '1.0.0',
        ]);

        // Send POST request to activate the module
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
        $this->assertEquals(trans('ModulesManager::messages.module_activated'), $payload['data']['message']);
        $this->assertEquals('active', $payload['data']['module']['status']);
        $this->assertEquals($module->name, $payload['data']['module']['name']);
    }

    /**
     * Test activation attempt of a non-existent module (should return 404).
     */
    public function testActivateModuleNotFound()
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
