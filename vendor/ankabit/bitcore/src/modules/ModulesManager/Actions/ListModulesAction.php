<?php

declare(strict_types=1);

namespace BitCore\Modules\ModulesManager\Actions;

use Psr\Http\Message\ResponseInterface as Response;
use OpenApi\Attributes as OA;

/**
 * Class ListModulesAction
 *
 * Handles fetching a list of modules with optional filters and sorting.
 */
class ListModulesAction extends ModulesManagerAction
{
    /**
     * Retrieve a list of modules with optional filters and sorting.
     *
     * @return Response JSON response containing the list of modules.
     */
    #[OA\Get(
        path: "[ROUTE:modules.index]",
        summary: "List all modules",
        description: "Fetches a list of all modules with optional filtering and sorting.",
        tags: ["Modules Manager"],
        parameters: [
            new OA\Parameter(
                name: "filters",
                in: "query",
                required: false,
                description: "Filters for searching modules (e.g., type, status).",
                schema: new OA\Schema(type: "object", example: '{"type": "custom"}')
            ),
            new OA\Parameter(
                name: "sort_by",
                in: "query",
                required: false,
                description: "Column to sort the modules by.",
                schema: new OA\Schema(type: "string", example: "priority")
            ),
            new OA\Parameter(
                name: "sort_order",
                in: "query",
                required: false,
                description: "Sorting order (ASC or DESC).",
                schema: new OA\Schema(type: "string", example: "ASC")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of modules retrieved successfully",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: true),
                        new OA\Property(property: "modules", type: "array", items: new OA\Items(
                            type: "object",
                            properties: [
                                new OA\Property(property: "name", type: "string", example: "ModuleName"),
                                new OA\Property(property: "priority", type: "integer", example: 5),
                                new OA\Property(property: "status", type: "string", example: "active"),
                                new OA\Property(property: "type", type: "string", example: "custom"),
                                new OA\Property(property: "plan", type: "string", example: "free"),
                                new OA\Property(
                                    property: "description",
                                    type: "string",
                                    example: "This is a module description."
                                ),
                                new OA\Property(
                                    property: "images",
                                    type: "array",
                                    items: new OA\Items(type: "string", example: "/path/to/image.jpg")
                                )
                            ]
                        ))
                    ]
                )
            )
        ]
    )]
    protected function action(): Response
    {
        // Retrieve query parameters for filtering and sorting
        $queryParams = (array) $this->request->getQueryParams();

        // Fetch modules using repository method
        $modules = $this->modulesManagerRepository->fetchWithFilters($queryParams);

        // Return response with modules list
        return $this->respondWithData([
            'status' => true,
            'message' => trans('ModulesManager::messages.modules_fetched'),
            'modules' => $modules,
        ], 200);
    }
}
