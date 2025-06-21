<?php

declare(strict_types=1);

namespace BitCore\Modules\Settings\Tests\Actions;

use BitCore\Modules\Settings\Tests\TestCase;

class ListSettingsActionTest extends TestCase
{
    public function testListSettingsSuccess()
    {
        $app = $this->getAppInstance();
        $routeUrl = $this->generateRouteUrl('settings.index');
        // Mock repository behavior - save some settings
        $group = 'module';
        $groupId = 'test';

        $this->settingsRepository->useGroup($group, $groupId)->saveMany([
            'site_name' => 'MyApp',
            'admin_email' => 'admin@example.com',
        ]);

        // Make a GET request to retrieve all settings
        $request = $this->createRequest('GET', $routeUrl);
        $response = $app->handle($request);

        // Get response payload
        $payload = json_decode((string) $response->getBody(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($payload['data']['status']);
        $this->assertEquals(trans('Settings::messages.settings_loaded'), $payload['data']['message']);
        $this->assertNotEmpty($payload['data']['data']);
        $this->assertNotEmpty($payload['data']['data'][$group]);
        $this->assertNotEmpty($payload['data']['data'][$group][$groupId]);
        $this->assertEquals('admin@example.com', $payload['data']['data'][$group][$groupId]['admin_email']['value']);
    }

    public function testSettingsListFetchByGroupSuccess()
    {
        $app = $this->getAppInstance();

        // Mock repository behavior - save some settings
        $group = 'module';
        $groupId = 'test';
        $routeUrl = $this->generateRouteUrl('settings.group', ['group' => $group]);

        $this->settingsRepository->useGroup($group, $groupId)->saveMany([
            'site_name' => 'MyApp',
            'admin_email' => 'admin@example.com',
        ]);

        $groupIdAlt = 'auth';
        $this->settingsRepository->useGroup($group, $groupIdAlt)->saveMany([
            'site_name' => 'MyApp',
            'admin_email' => 'admin@group.com',
        ]);

        // Make a GET request to retrieve all settings
        $request = $this->createRequest('GET', $routeUrl);
        $response = $app->handle($request);

        // Get response payload
        $payload = json_decode((string) $response->getBody(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($payload['data']['status']);
        $this->assertEquals(trans('Settings::messages.settings_loaded'), $payload['data']['message']);
        $this->assertNotEmpty($payload['data']['data']);
        $this->assertNotEmpty($payload['data']['data'][$groupIdAlt]);
        $this->assertEquals('admin@group.com', $payload['data']['data'][$groupIdAlt]['admin_email']['value']);
    }

    public function testSettingsListFetchByGroupAndGroupIdSuccess()
    {
        $app = $this->getAppInstance();

        // Mock repository behavior - save some settings
        $group = 'module';
        $groupId = 'test';

        $this->settingsRepository->useGroup($group, $groupId)->saveMany([
            'site_name' => 'MyApp',
            'admin_email' => 'admin@example.com',
        ]);

        $groupIdAlt = 'auth';
        $routeUrl = $this->generateRouteUrl('settings.groupItem', ['group' => $group, 'id' => $groupIdAlt]);

        $this->settingsRepository->useGroup($group, $groupIdAlt)->saveMany([
            'site_name' => 'MyApp',
            'admin_email' => 'admin@group.com',
        ]);

        // Make a GET request to retrieve all settings
        $request = $this->createRequest('GET', $routeUrl);
        $response = $app->handle($request);

        // Get response payload
        $payload = json_decode((string) $response->getBody(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($payload['data']['status']);
        $this->assertEquals(trans('Settings::messages.settings_loaded'), $payload['data']['message']);
        $this->assertNotEmpty($payload['data']['data']);
        $this->assertEquals('admin@group.com', $payload['data']['data']['admin_email']['value']);
    }

    public function testListSettingsEmpty()
    {
        $app = $this->getAppInstance();

        // Ensure no settings exist in the repository
        $this->settingsRepository->saveMany([]);
        $routeUrl = $this->generateRouteUrl('settings.index', ['group' => 'nonExistingGroup', 'id' => 'wrongId']);

        // Make a GET request to retrieve settings
        $request = $this->createRequest('GET', $routeUrl);
        $response = $app->handle($request);

        // Get response payload
        $payload = json_decode((string) $response->getBody(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($payload['data']['status']);
        $this->assertEquals(trans('Settings::messages.settings_loaded'), $payload['data']['message']);
        $this->assertArrayHasKey('data', $payload);
        $this->assertEmpty($payload['data']['data']);
    }
}
