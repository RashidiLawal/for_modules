<?php

declare(strict_types=1);

namespace Modules\Todo\Actions\Groups;

use Psr\Http\Message\ResponseInterface as Response;
use Modules\Todo\Requests\Groups\ListAllGroupsRequest;
use OpenApi\Attributes as OA;

/**
 * Class ListhAllGroupsAction
 *
 * Handles listing and filtering todo groups.
 */
class ListAllGroupsAction extends GroupAction
{
    #[OA\Get(
        path: "[ROUTE:groups.index]",
        summary: "Fetch all affiliate groups",
        description: "Retrieve a paginated list of affiliate groups with filters and sorting.",
        tags: ["Affiliate Module"],
        parameters: [
            new OA\Parameter(
                name: "search",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "string"),
                example: "elite"
            ),
            new OA\Parameter(
                name: "status",
                in: "query",
                required: false,
                schema: new OA\Schema(
                    type: "string",
                    enum: ["active", "inactive"]
                )
            ),
            new OA\Parameter(
                name: "sort_by",
                in: "query",
                required: false,
                schema: new OA\Schema(
                    type: "string",
                    enum: ["group_name", "created_at"]
                )
            ),
            new OA\Parameter(
                name: "order",
                in: "query",
                required: false,
                schema: new OA\Schema(
                    type: "string",
                    enum: ["asc", "desc"]
                ),
                example: "asc"
            ),
            new OA\Parameter(
                name: "page",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "integer"),
                example: 1
            ),
            new OA\Parameter(
                name: "per_page",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "integer"),
                example: 10
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of affiliate groups",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Groups fetched successfully."),
                        new OA\Property(
                            property: "groups",
                            type: "array",
                            items: new OA\Items(ref: "#/components/schemas/AffiliateGroup")
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: "Unexpected error",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "An unexpected error occurred."),
                        new OA\Property(property: "error", type: "string")
                    ]
                )
            )
        ]
    )]
    protected function action(): Response
    {
        try {
            $queryParams = ListAllGroupsRequest::data();
            $groups = $this->groupRepository->fetchWithFilters($queryParams);

            return $this->respondWithData([
                'status' => true,
                'message' => trans("Todo::messages.groups_fetched"),
                'groups' => $groups
            ]);
        } catch (\Exception $e) {
            $this->logger->error("Failed to fetch todo groups: " . $e->getMessage());

            return $this->respondWithData([
                'status' => false,
                'error' => $e->getMessage(),
                'message' => trans("Todo::messages.groups_fetch_failed"),
            ], 500);
        }
    }
}
