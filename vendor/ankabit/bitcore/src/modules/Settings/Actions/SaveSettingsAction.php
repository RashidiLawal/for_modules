<?php

declare(strict_types=1);

namespace BitCore\Modules\Settings\Actions;

use Psr\Http\Message\ResponseInterface as Response;
use BitCore\Modules\Settings\Requests\CreateSettingsRequest;
use BitCore\Modules\Settings\Requests\SaveSettingsRequest;
use OpenApi\Attributes as OA;

class SaveSettingsAction extends SettingsAction
{
    /**
     * Execute saving settings at once.
     *
     * @return Response JSON response confirming the save.
     */
    #[OA\Post(
        path: "[ROUTE:settings.store]",
        summary: "Save multiple settings",
        description: "Saves or updates multiple settings grouped by system and subgroup.",
        tags: ["Settings Module"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(
                        property: "settings",
                        type: "object",
                        description: "Grouped settings data",
                        example: [
                            "system" => [
                                "default" => [
                                    "site_name" => "My Website",
                                    "site_url" => "https://example.com"
                                ],
                                "email" => [
                                    "smtp_host" => "smtp.example.com",
                                    "smtp_port" => 587
                                ]
                            ]
                        ]
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Settings saved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Settings saved successfully")
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Invalid request data",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Invalid data provided")
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: "Internal server error",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Failed to save settings")
                    ]
                )
            )
        ]
    )]
    protected function action(): Response
    {
        // Get validated data from the request
        $data = SaveSettingsRequest::validated();

        // Retrieve the settings array from the validated data
        $settings = $data['settings']; // i.e settings[system][default][key]=value

        // Check if the settings array is valid
        if (!is_array($settings) || empty($settings)) {
            return $this->respondWithData([
                'status'  => false,
                'message' => trans('Settings::messages.invalid_data'),
            ], 400);
        }

        // Loop through the settings to group them by their structure
        foreach ($settings as $group => $subGroups) {
            foreach ($subGroups as $subGroup => $settingValues) {
                if (empty($group) || empty($subGroup)) {
                    continue;
                }

                // Filter setting values and remove unkown or unregistered settings
                //$settingValues;
                $this->settingsRepository->useGroup($group, $subGroup)->saveMany($settingValues);
            }
        }

        return $this->respondWithData([
            'status'  => true,
            'message' => trans('Settings::messages.settings_saved'),
        ], 200);
    }
}
