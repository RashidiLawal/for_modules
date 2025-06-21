<?php

declare(strict_types=1);

namespace Modules\Affiliate\Actions\Groups;

use Psr\Http\Message\ResponseInterface as Response;
use OpenApi\Attributes as OA;

/**
 * Delete a Group.
 */
class DeleteGroupAction extends GroupAction
{
    #[OA\Delete(
        path: "[ROUTE:groups.delete]",
        summary: "Delete a group",
        description: "Removes an affiliate group by ID.",
        tags: ["Affiliate Module"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer"),
                description: "The ID of the group to delete"
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Group deleted successfully"),
            new OA\Response(response: 404, description: "Group not found"),
            new OA\Response(response: 500, description: "Server error")
        ]
    )]
    protected function action(): Response
    {
        $id = (int) $this->resolveArg('id');

        $group = $this->groupRepository->findById($id);

        if (empty($group->id)) {
            return $this->respondWithData([
                'status' => false,
                'message' => trans("Affiliate::messages.group_not_found"),
            ], 404);
        }
        $this->groupRepository->delete($id);

        return $this->respondWithData([
            'status'  => true,
            'message' => trans("Affiliate::messages.group_deleted"),
        ], 200);
    }
}
