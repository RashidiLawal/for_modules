<?php

declare(strict_types=1);

namespace BitCore\Modules\Settings\Tests\Actions;

use BitCore\Modules\Settings\Tests\TestCase;

class SaveSettingsActionTest extends TestCase
{
    /**
     * Generate route for delete bulk lead by ID.
     */
    private function getRoute(): string
    {
        return $this->generateRouteUrl('settings.store');
    }

    public function testUpdateSettingSuccess()
    {
        $app = $this->getAppInstance();
        $group = 'module';
        $groupId = 'test';

        // Define request payload
        $requestData = [
            'settings' => [
                "$group" => [
                    "$groupId" => [
                        'admin_email' => 'updated@example.com'
                    ]
                ]
            ]
        ];

        // Make a POST request to update the setting
        $request = $this->createRequestWithCsrf($this->app, $this->getRoute(), 'POST', $requestData);
        $response = $app->handle($request);

        // Get response payload
        $payload = json_decode((string) $response->getBody(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($payload['data']['status']);
        $this->assertEquals(trans('Settings::messages.settings_saved'), $payload['data']['message']);

        // Verify that the setting was updated in the repository
        $settings = $this->settingsRepository->load()->useGroup($group, $groupId)->getAll();
        $savedEmail = $this->settingsRepository->useGroup($group, $groupId)->get('admin_email');

        $this->assertEquals('updated@example.com', $savedEmail);
    }

    public function testUpdateSettingValidationError()
    {
        $app = $this->getAppInstance();

        // Send a request with missing 'value' field
        $requestData = [];

        $request = $this->createRequestWithCsrf($this->app, $this->getRoute(), 'POST', $requestData);
        $response = $app->handle($request);

        // Get response payload
        $payload = json_decode((string) $response->getBody(), true);

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertArrayHasKey('error', $payload['data']);
    }
}
