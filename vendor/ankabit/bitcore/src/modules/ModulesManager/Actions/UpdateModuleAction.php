<?php

declare(strict_types=1);

namespace BitCore\Modules\ModulesManager\Actions;

use Psr\Http\Message\ResponseInterface as Response;
use BitCore\Modules\ModulesManager\Requests\UpdateModuleStatusRequest;
use OpenApi\Attributes as OA;

/**
 * Class UpdateModuleAction
 *
 * Handles the update process for an existing module.
 */
class UpdateModuleAction extends ModulesManagerAction
{
    /**
     * Update an existing module.
     *
     * @return Response JSON response indicating success or failure.
     */
    #[OA\Put(
        path: "[ROUTE:modules.update]",
        summary: "Update a module",
        description: "Updates an existing module with new data.",
        tags: ["Modules Manager"],
        parameters: [
            new OA\Parameter(
                name: "name",
                in: "path",
                required: true,
                description: "Name of the module to be updated",
                schema: new OA\Schema(type: "integer", example: 5)
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: "Module data to be updated",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(
                        property: "name",
                        type: "string",
                        example: "Updated Module Name"
                    ),
                    new OA\Property(
                        property: "description",
                        type: "string",
                        example: "Updated description for the module."
                    ),
                    new OA\Property(property: "status", type: "boolean", example: true)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Module updated successfully",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: true),
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: "Module updated successfully."
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Failed to update module",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: false),
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: "Failed to update module."
                        )
                    ]
                )
            )
        ]
    )]
    protected function action(): Response
    {
        // Retrieve module ID from route parameter
        $name = $this->resolveArg('name');

        // Validate the request data
        $data = UpdateModuleStatusRequest::validated();

        // Find the module by ID
        $module = $this->modulesManagerRepository->findByName($name);
        if (!$module) {
            return $this->respondWithData([
                'status' => false,
                'message' => trans("ModulesManager::messages.module_not_found"),
            ], 404);
        }

        // Attempt to update the module
        $updated = $this->modulesManagerRepository->update($module->id, $data);

        if (empty($updated->id)) {
            return $this->respondWithData([
                'status' => false,
                'message' => trans("ModulesManager::messages.module_update_failed"),
            ], 400);
        }

        return $this->respondWithData([
            'status' => true,
            'message' => trans("ModulesManager::messages.module_updated"),
        ], 200);
    }
}
