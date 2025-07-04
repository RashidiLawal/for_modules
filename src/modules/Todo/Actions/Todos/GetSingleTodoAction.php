<?php

declare(strict_types=1);

namespace Modules\Todo\Actions\Todos;

use Psr\Http\Message\ResponseInterface as Response;
use OpenApi\Attributes as OA;

/**
 * Class GetSingleTodoAction
 *
 * Retrieves a single todo by its ID.
 */
class GetSingleTodoAction extends TodoAction
{
    /**
     * Handles the retrieval of a single todo by ID.
     *
     * @return Response JSON response containing the todo data or an error.
     */
    #[OA\Get(
        path: "[ROUTE:affiliates.show]",
        summary: "Get a single affiliate",
        description: "Returns a single affiliate by its unique ID.",
        tags: ["Affiliate Module"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "The ID of the affiliate",
                schema: new OA\Schema(type: "integer", example: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Affiliate fetched successfully.",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: true),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "name", type: "string", example: "John Doe"),
                                new OA\Property(property: "slug", type: "string", example: "john-doe"),
                                new OA\Property(property: "email", type: "string", example: "john.doe@example.com"),
                                new OA\Property(
                                    property: "commission_rate",
                                    type: "number",
                                    format: "float",
                                    example: 5.5
                                ),
                                new OA\Property(property: "status", type: "string", example: "active"),
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Affiliate not found."
            ),
            new OA\Response(
                response: 500,
                description: "Internal server error."
            )
        ]
    )]
    protected function action(): Response
    {
        $id = (int) $this->resolveArg('id');

        try {
            $todo = $this->todoRepository->findById($id);

            if (!$todo) {
                return $this->respondWithData([
                    'status' => false,
                    'message' => trans("Todo::messages.todo_not_found"),
                ], 404);
            }

            return $this->respondWithData([
                'status' => true,
                'message' => trans("Todo::messages.todo_fetched"),
                'data' => $todo,
            ]);
        } catch (\Exception $e) {
            $this->logger->error("Error fetching affiliate with ID {$id}: " . $e->getMessage());

            return $this->respondWithData([
                'status' => false,
                'message' => trans("Todo::messages.unexpected_error"),
            ], 500);
        }
    }
}
