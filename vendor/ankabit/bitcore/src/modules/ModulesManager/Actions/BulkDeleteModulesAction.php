<?php

declare(strict_types=1);

namespace BitCore\Modules\ModulesManager\Actions;

use Psr\Http\Message\ResponseInterface as Response;
use OpenApi\Attributes as OA;

/**
 * Class BulkDeleteModulesAction
 *
 * Handles bulk deletion of modules.
 */
class BulkDeleteModulesAction extends ModulesManagerAction
{
    /**
     * Executes the action to bulk delete modules.
     *
     * @return Response JSON response.
     */
    #[OA\Delete(
        path: "[ROUTE:modules.bulkDelete]",
        summary: "Bulk delete modules",
        description: "Deletes multiple modules at once based on provided module IDs.",
        tags: ["Modules Manager"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                required: ["module_ids"],
                properties: [
                    new OA\Property(
                        property: "module_ids",
                        type: "array",
                        description: "Array of module IDs to be deleted.",
                        items: new OA\Items(type: "integer"),
                        example: [1, 2, 3, 4]
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Successfully deleted the modules",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: true),
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: "Modules deleted successfully."
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Invalid module IDs provided",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Invalid module IDs."),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Bulk deletion failed",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Bulk module delete failed."),
                    ]
                )
            ),
        ]
    )]
    protected function action(): Response
    {
        // Retrieve module IDs from the request
        $data = $this->getFormData();
        $moduleIds = $data['module_ids'] ?? [];

        // Validate the input
        if (empty($moduleIds) || !is_array($moduleIds)) {
            return $this->respondWithData([
                'status' => false,
                'message' => trans("ModulesManager::messages.invalid_module_ids"),
            ], 400); // 400 Bad Request
        }

        // Attempt to delete the modules
        $deleted = $this->modulesManagerRepository->bulkDelete($moduleIds);

        if (!$deleted) {
            return $this->respondWithData([
                'status' => false,
                'message' => trans("ModulesManager::messages.bulk_modules_delete_failed"),
            ], 401);
        }

        // Log the bulk deletion event
        $this->logger->info("Modules deleted: " . implode(',', $moduleIds));

        return $this->respondWithData([
            'status' => true,
            'message' => trans("ModulesManager::messages.bulk_modules_delete_success"),
        ], 200);
    }
}
