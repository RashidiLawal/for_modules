<?php

declare(strict_types=1);

namespace Modules\Todo\Actions\Groups;

use BitCore\Application\Actions\Action;
use Modules\Todo\Repositories\Groups\GroupRepositoryInterface;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Todo Groups",
    description: "Operations related to affiliate groups management.
    \n\n**Postman Collection:** 
    [Download the Groups Module Collection](/api/postman?name=groups)"
)]
#[OA\Schema(
    schema: "AffiliateGroup",
    description: "Group schema representing affiliate group settings",
    type: "object",
    properties: [
        new OA\Property(property: "name", type: "string", example: "Gold Partners"),
        new OA\Property(property: "default_commission_rate", type: "number", format: "float", example: 10.0),
        new OA\Property(property: "auto_approve_affiliates", type: "boolean", example: true),
        new OA\Property(
            property: "description",
            type: "string",
            nullable: true,
            example: "Affiliates in this group get higher commissions and early access to campaigns."
        ),
        new OA\Property(
            property: "created_at",
            type: "string",
            format: "date-time",
            example: "2025-04-25T14:00:00Z"
        )
    ]
)]

/**
 * Summary of GroupAction
 * @package Modules\Todo\Actions\Groups
 * The Base Action for all Group related actions.
 * It initializes the GroupRepositoryInterface for use in derived classes.
 * This abstract class provides common functionality for group-related actions,
 * 
 */
abstract class GroupAction extends Action
{
    protected GroupRepositoryInterface $groupRepository;

    protected function afterConstruct(): void
    {
        $this->groupRepository = $this->container->get(GroupRepositoryInterface::class);
    }
}
