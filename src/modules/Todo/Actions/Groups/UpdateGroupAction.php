<?php

declare(strict_types=1);

namespace Modules\Todo\Actions\Groups;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Modules\Todo\Requests\Groups\UpdateGroupRequest;
use OpenApi\Attributes as OA;

/**
 * Class EditGroupAction
 *
 * Updates an existing affiliate group.
 */
class UpdateGroupAction extends GroupAction
{
    /**
     * Handle the update request for an affiliate group.
     *
     * @return Response JSON response with updated group or error.
     */
    #[OA\Put(
        path: "[ROUTE:groups.update]",
        summary: "Update an existing affiliate group",
        description: "Updates an affiliate group with the provided data by ID.",
        tags: ["Affiliate Module"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "The ID of the group to update",
                schema: new OA\Schema(type: "integer", example: 1)
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "commission_type", "commission_value"],
                type: "object",
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Gold Affiliates"),
                    new OA\Property(property: "commission_type", type: "string", example: "percentage"),
                    new OA\Property(property: "commission_value", type: "number", format: "float", example: 10.5),
                    new OA\Property(property: "auto_approve", type: "boolean", example: true),
                    new OA\Property(
                        property: "description",
                        type: "string",
                        nullable: true,
                        example: "Top-tier group for trusted affiliates"
                    ),
                    new OA\Property(property: "status", type: "string", example: "active"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Group updated successfully."),
            new OA\Response(response: 422, description: "Validation failed."),
            new OA\Response(response: 500, description: "Server error."),
        ]
    )]
    protected function action(): Response
    {
        try {
            $id = (int) $this->resolveArg('id');
            $data = UpdateGroupRequest::data();

            // Validate input
            $errors = UpdateGroupRequest::validate($data);
            if ($errors) {
                return $this->respondWithData([
                    'status' => false,
                    'errors' => $errors->all(),
                ], 422);
            }

            // Update group
            $group = $this->groupRepository->findById($id);

            if (empty($group->id)) {
                return $this->respondWithData([
                    'status' => false,
                    'message' => trans("Affiliate::messages.group_not_found"),
                ], 404);
            }
            $updated = $this->groupRepository->update($id, $data);

            return $this->respondWithData([
                'status' => true,
                'message' => trans("Affiliate::messages.group_updated"),
                'data' => $updated,
            ]);
        } catch (Exception $e) {
            $this->logger->error("Group update failed: " . $e->getMessage(), [
                'exception' => $e,
            ]);

            return $this->respondWithData([
                'status' => false,
                'error' => $e->getMessage(),
                'message' => trans("Affiliate::messages.unexpected_error"),
            ], 500);
        }
    }
}
