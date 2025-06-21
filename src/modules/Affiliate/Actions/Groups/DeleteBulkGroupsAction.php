<?php

declare(strict_types=1);

namespace Modules\Affiliate\Actions\Groups;

use Psr\Http\Message\ResponseInterface as Response;
use OpenApi\Attributes as OA;

/**
 * Class DeleteBulkGroupsAction
 *
 * Handles the bulk deletion of affiliate groups.
 */
class DeleteBulkGroupsAction extends GroupAction
{
    #[OA\Post(
        path: "[ROUTE:groups.bulkDelete]",
        summary: "Delete multiple affiliate groups",
        description: "Deletes multiple affiliate groups based on
        the provided array of group IDs.",
        tags: ["Affiliate Module"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                required: ["ids"],
                properties: [
                    new OA\Property(
                        property: "ids",
                        type: "array",
                        items: new OA\Items(type: "integer"),
                        example: [1, 2, 3],
                        description: "Array of group IDs to delete"
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Groups deleted successfully",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: true),
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: "Groups deleted successfully."
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Invalid input",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Invalid ID list")
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: "Internal server error",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Group deletion failed")
                    ]
                )
            )
        ]
    )]
    protected function action(): Response
    {
        $params = (array) $this->getFormData();

        if (!isset($params['ids']) || !is_array($params['ids'])) {
            return $this->respondWithData([
                'status'  => false,
                'message' => trans("Affiliate::messages.invalid_id_list"),
            ], 422);
        }

        $deleted = $this->groupRepository->bulkDelete($params['ids']);

        return $this->respondWithData([
            'status'  => $deleted ? true : false,
            'message' => $deleted
                ? trans("Affiliate::messages.bulk_group_delete_success")
                : trans("Affiliate::messages.bulk_group_delete_failed"),
        ], $deleted ? 200 : 500);
    }
}
