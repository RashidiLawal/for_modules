<?php

declare(strict_types=1);

namespace Modules\Todo\Actions\Groups;

use Psr\Http\Message\ResponseInterface as Response;
use OpenApi\Attributes as OA;

/**
 * Class GetSingleGroupAction
 *
 * Retrieves a single todo group by its ID.
 */
class GetSingleGroupAction extends GroupAction
{
    /**
     * Handles the retrieval of a single todo group by ID.
     *
     * @return Response JSON response containing the group data or an error.
     */
    #[OA\Get(
        path: "[ROUTE:groups.show]",
        summary: "Get a single affiliate group",
        description: "Returns a single affiliate group by its unique ID.",
        tags: ["Affiliate Module"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "The ID of the affiliate group",
                schema: new OA\Schema(type: "integer", example: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Group fetched successfully.",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: true),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "group_name", type: "string", example: "Elite Group"),
                                new OA\Property(property: "group_slug", type: "string", example: "elite-group"),
                                new OA\Property(property: "clicks_generated", type: "integer", example: 0),
                                new OA\Property(
                                    property: "total_earnings",
                                    type: "number",
                                    format: "float",
                                    example: 0.00
                                ),
                                new OA\Property(property: "status", type: "string", example: "active"),
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Group not found."
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
            $group = $this->groupRepository->findById($id);

            if (!$group) {
                return $this->respondWithData([
                    'status' => false,
                    'message' => trans("Todo::messages.group_not_found"),
                ], 404);
            }

            return $this->respondWithData([
                'status' => true,
                'message' => trans("Todo::messages.group_fetched"),
                'data' => $group,
            ]);
        } catch (\Exception $e) {
            $this->logger->error("Error fetching group with ID {$id}: " . $e->getMessage());

            return $this->respondWithData([
                'status' => false,
                'message' => trans("Todo::messages.unexpected_error"),
            ], 500);
        }
    }
}
