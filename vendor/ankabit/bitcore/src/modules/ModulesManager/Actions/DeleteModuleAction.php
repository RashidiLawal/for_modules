<?php

declare(strict_types=1);

namespace BitCore\Modules\ModulesManager\Actions;

use Psr\Http\Message\ResponseInterface as Response;
use OpenApi\Attributes as OA;

/**
 * Class DeleteModuleAction
 *
 * Handles the deletion of a module by its unique name.
 */
class DeleteModuleAction extends ModulesManagerAction
{
    /**
     * Delete a module by ID.
     *
     * @return Response JSON response indicating success or failure.
     */
    #[OA\Delete(
        path: "[ROUTE:modules.delete]",
        summary: "Delete a module",
        description: "Deletes an existing module by its name.",
        tags: ["Modules Manager"],
        parameters: [
            new OA\Parameter(
                name: "name",
                in: "path",
                required: true,
                description: "Name of the module to delete",
                schema: new OA\Schema(type: "integer", example: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Module deleted successfully",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: true),
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: "Module deleted successfully."
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Module not found",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: false),
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: "Module not found."
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Module deletion failed",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: false),
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: "Failed to delete module."
                        )
                    ]
                )
            )
        ]
    )]
    protected function action(): Response
    {
        // Get the module name from the request arguments
        $name = $this->resolveArg('name');

        // Check if the module exists
        $module = $this->modulesManagerRepository->findByName($name);
        if (!$module) {
            return $this->respondWithData([
                'status' => false,
                'message' => trans("ModulesManager::messages.module_not_found"),
            ], 404);
        }

        if ($module->isCore()) {
            return $this->respondWithData([
                'status' => false,
                'module' => $module,
                'message' => trans("ModulesManager::messages.action_not_allow_on_core_module"),
            ], 403);
        }

        $deleted = $this->modulesManagerRepository->uninstall($module);

        // If deletion failed, return an error response
        if (!$deleted) {
            return $this->respondWithData([
                'status' => false,
                'message' => trans("ModulesManager::messages.module_delete_failed"),
            ], 400);
        }

        // Return a success response
        return $this->respondWithData([
            'status' => true,
            'message' => trans("ModulesManager::messages.module_deleted"),
        ], 200);
    }
}
