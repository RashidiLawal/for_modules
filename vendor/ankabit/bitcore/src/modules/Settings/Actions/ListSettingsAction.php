<?php

declare(strict_types=1);

namespace BitCore\Modules\Settings\Actions;

use Psr\Http\Message\ResponseInterface as Response;
use OpenApi\Attributes as OA;

/**
 * Class ListSettingsAction
 * Retrieves a list of Settingss with optional filtering and sorting.
 */
class ListSettingsAction extends SettingsAction
{
    /**
     * Execute loading all settings from the database.
     *
     * @return Response JSON response containing all settings.
     */
    #[OA\Get(
        path: "[ROUTE:settings.index]",
        summary: "Retrieve application settings for a group item (key)",
        description: "Fetches all available settings for the given group and specific key",
        tags: ["Settings Module"],
        parameters: [
            new OA\Parameter(
                name: "group",
                in: "path",
                required: false,
                description: "The group of settings to retrieve",
                schema: new OA\Schema(type: "string")
            ),
            new OA\Parameter(
                name: "id",
                in: "path",
                required: false,
                description: "The specific setting key within the group to retrieve",
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Settings loaded successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "boolean"),
                        new OA\Property(property: "message", type: "string"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "key", type: "string"),
                                new OA\Property(property: "value", type: "string"),
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: "Internal server error",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Failed to load settings")
                    ]
                )
            )
        ]
    )]
    #[OA\Get(
        path: "[ROUTE:settings.group]",
        summary: "Retrieve application settings for a group",
        description: "Fetches all available settings by group.",
        tags: ["Settings Module"],
        parameters: [
            new OA\Parameter(
                name: "group",
                in: "path",
                required: false,
                description: "The group of settings to retrieve",
                schema: new OA\Schema(type: "string")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Settings loaded successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "boolean"),
                        new OA\Property(property: "message", type: "string"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "key", type: "string"),
                                new OA\Property(property: "value", type: "string"),
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: "Internal server error",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Failed to load settings")
                    ]
                )
            )
        ]
    )]
    #[OA\Get(
        path: "[ROUTE:settings.groupItem]",
        summary: "Retrieve application settings for a group item (key)",
        description: "Fetches all available settings for the given group and specific key",
        tags: ["Settings Module"],
        parameters: [
            new OA\Parameter(
                name: "group",
                in: "path",
                required: false,
                description: "The group of settings to retrieve",
                schema: new OA\Schema(type: "string")
            ),
            new OA\Parameter(
                name: "id",
                in: "path",
                required: false,
                description: "The specific setting key within the group to retrieve",
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Settings loaded successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "boolean"),
                        new OA\Property(property: "message", type: "string"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "key", type: "string"),
                                new OA\Property(property: "value", type: "string"),
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: "Internal server error",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Failed to load settings")
                    ]
                )
            )
        ]
    )]
    protected function action(): Response
    {
        $group = $this->resolveArg('group', false);
        $groupId = $this->resolveArg('id', false);

        $allSettings = $this->settingsRepository->getAll();
        $settings = $allSettings;

        if ($group) {
            $settings = $allSettings[$group] ?? [];
        }

        if ($group && $groupId) {
            $settings = $allSettings[$group][$groupId] ?? [];
        }

        return $this->respondWithData([
            'status'  => true,
            'message' => trans('Settings::messages.settings_loaded'),
            'data'    => $settings,
        ], 200);
    }
}
