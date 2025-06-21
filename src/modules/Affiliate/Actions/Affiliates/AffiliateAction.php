<?php

declare(strict_types=1);

namespace Modules\Affiliate\Actions\Affiliates;

use BitCore\Application\Actions\Action;
use Modules\Affiliate\Repositories\Affiliates\AffiliateRepositoryInterface;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Affiliate Module",
    description: "Operations related to affiliate management.
    \n\n**Postman Collection:** 
    [Download the Affiliate Module Collection](/api/postman?name=affiliate)"
)]
#[OA\Schema(
    schema: "Affiliate",
    description: "Affiliate schema",
    type: "object",
    properties: [
        new OA\Property(property: "name", type: "string", example: "Jane Doe"),
        new OA\Property(property: "email", type: "string", format: "email", example: "janedoe@example.com"),
        new OA\Property(property: "group_id", type: "integer", example: 1),
        new OA\Property(property: "commission_rate", type: "number", format: "float", example: 15.0),
        new OA\Property(property: "total_earned", type: "number", format: "float", example: 500.75),
        new OA\Property(property: "is_active", type: "boolean", example: true),
        new OA\Property(
            property: "joined_at",
            type: "string",
            format: "date-time",
            example: "2025-04-30T10:30:00Z"
        ),
        new OA\Property(
            property: "notes",
            type: "string",
            nullable: true,
            example: "Top performing affiliate this quarter"
        )
    ]
)]
abstract class AffiliateAction extends Action
{
    protected AffiliateRepositoryInterface $affiliateRepository;

    protected function afterConstruct(): void
    {
        $this->affiliateRepository = $this->container->get(AffiliateRepositoryInterface::class);
    }
}
