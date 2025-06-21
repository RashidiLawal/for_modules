<?php

declare(strict_types=1);

namespace BitCore\Modules\ModulesManager\Actions;

use Psr\Http\Message\ResponseInterface as Response;
use OpenApi\Attributes as OA;

/**
 * Class GetModuleAction
 * Retrieves module details by name.
 */
#[OA\Get(
    path: "[ROUTE:modules.show]",
    summary: "Fetch a single module by name",
    description: "Retrieves detailed information about a specific module.",
    tags: ["Modules Manager"],
    parameters: [
        new OA\Parameter(
            name: "module",
            in: "path",
            description: "The unique identifier of the module",
            required: true,
            schema: new OA\Schema(type: "integer", example: 1)
        ),
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: "Module retrieved successfully",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "status", type: "boolean", example: true),
                    new OA\Property(property: "message", type: "string", example: "Module fetched successfully"),
                    new OA\Property(property: "module", ref: "#/components/schemas/ModulesManager"),
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
                    new OA\Property(property: "message", type: "string", example: "Module not found"),
                ]
            )
        ),
    ]
)]
class GetModuleAction extends ModulesManagerAction
{
    /**
     * Execute the module retrieval process.
     *
     * @return Response JSON response containing module details or an error message.
     */
    protected function action(): Response
    {
        // Retrieve module ID from route parameters
        $name = $this->resolveArg('name');

        // Fetch module details from repository
        $module = $this->modulesManagerRepository->findByName($name);
        if (!$module) {
            return $this->respondWithData([
                'status' => false,
                'message' => trans("ModulesManager::messages.module_not_found"),
            ], 404);
        }

        return $this->respondWithData([
            'status'  => true,
            'message' => trans('ModulesManager::messages.module_fetched'),
            'module'  => $module,
        ], 200);
    }
}
