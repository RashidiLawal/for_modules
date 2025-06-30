<?php

declare(strict_types=1);

namespace Modules\Todo\Actions\Groups;

use Psr\Http\Message\ResponseInterface as Response;
use Modules\Todo\Requests\Groups\CreateGroupRequest;
use OpenApi\Attributes as OA;

/**
 * Class CreateGroupAction
 *
 * Handles the creation of a new affiliate group.
 */
class CreateGroupAction extends GroupAction
{
    #[OA\Post(
        path: "[ROUTE:groups.store]",
        summary: "Create a new affiliate group",
        description: "Creates a new group with default settings for affiliates.",
        tags: ["Affiliate Module"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                required: ["group_name", "group_slug", "default_commission_rate"],
                properties: [
                    new OA\Property(property: "group_name", type: "string", example: "Elite Group"),
                    new OA\Property(property: "group_slug", type: "string", example: "elite-group"),
                    new OA\Property(property: "clicks_generated", type: "integer", example: 0),
                    new OA\Property(property: "total_earnings", type: "number", format: "float", example: 0.00),
                    new OA\Property(property: "is_auto_approved", type: "boolean", example: true),
                    new OA\Property(
                        property: "default_commission_rate",
                        type: "number",
                        format: "float",
                        example: 10.00
                    ),
                    new OA\Property(property: "commission_lock_period", type: "integer", example: 30),
                    new OA\Property(property: "reward_type", type: "string", example: "percentage"),
                    new OA\Property(property: "is_enable_commission", type: "boolean", example: true),
                    new OA\Property(property: "commission_rate", type: "number", format: "float", example: 5.00),
                    new OA\Property(property: "commission_type", type: "string", nullable: true, example: "flat"),
                    new OA\Property(property: "commission_rule", type: "string", nullable: true, example: "monthly"),
                    new OA\Property(property: "commission_amount", type: "number", format: "float", example: 100.00),
                    new OA\Property(property: "payout_minimum", type: "number", format: "float", example: 50.00),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Group created successfully"),
            new OA\Response(response: 422, description: "Validation errors"),
            new OA\Response(response: 500, description: "Internal server error")
        ]
    )]
    protected function action(): Response
    {
        try {
            $data = CreateGroupRequest::data();

            // Validate input
            $errors = CreateGroupRequest::validate($data);
            if ($errors) {
                return $this->respondWithData([
                    'status' => false,
                    'errors' => $errors->all(),
                ], 422);
            }

            // Check for duplicate name
            if ($this->groupRepository->nameExists($data['group_name'])) {
                return $this->respondWithData([
                    'status' => false,
                    'errors' => [trans("Affiliate::messages.group_name_exists")],
                ], 409);
            }

            // Check for duplicate slug
            if (!empty($data['group_slug']) && $this->groupRepository->findBySlug($data['group_slug'])) {
                return $this->respondWithData([
                    'status' => false,
                    'errors' => [trans("Affiliate::messages.group_slug_exists")],
                ], 409);
            }

            $group = $this->groupRepository->create($data);

            if (empty($group->id)) {
                return $this->respondWithData([
                    'status' => false,
                    'message' => trans("Affiliate::messages.group_creation_failed"),
                ], 400);
            }

            return $this->respondWithData([
                'status'  => true,
                'message' => trans("Affiliate::messages.group_created"),
                'data'    => $group,
            ], 201);
        } catch (\Exception $e) {
            $this->logger->error("Group creation failed: " . $e->getMessage(), [
                'exception' => $e
            ]);

            return $this->respondWithData([
                'status'  => false,
                'error'   => $e->getMessage(),
                'message' => trans("Affiliate::messages.unexpected_error"),
            ], 500);
        }
    }
}
