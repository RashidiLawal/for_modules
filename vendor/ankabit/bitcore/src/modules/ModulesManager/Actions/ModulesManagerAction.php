<?php

declare(strict_types=1);

namespace BitCore\Modules\ModulesManager\Actions;

use BitCore\Application\Actions\Action;
use BitCore\Application\Services\Modules\ModuleRegistry;
use BitCore\Modules\ModulesManager\Repositories\ModulesManagerRepositoryInterface;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Modules Manager",
    description: "Manage modules within the system including upload, enable/disable, delete, and more. 
    \n\n**Postman Collection:** 
    [Download the ModulesManager Collection]([POSTMAN_URL])"
)]
#[OA\Schema(
    schema: "ModulesManager",
    title: "Modules Manager",
    description: "Represents a module entry managed within the system.",
    type: "object",
    required: ["name", "priority"],
    properties: [
        new OA\Property(
            property: "id",
            type: "integer",
            description: "Unique identifier of the module",
            example: 1
        ),
        new OA\Property(
            property: "name",
            type: "string",
            description: "The name of the module",
            example: "Blog"
        ),
        new OA\Property(
            property: "priority",
            type: "integer",
            description: "Display or load priority of the module",
            example: 10
        ),
        new OA\Property(
            property: "entry",
            type: "string",
            nullable: true,
            description: "Entry point of the module (e.g., namespace or bootstrap file)",
            example: "Modules\\Blog\\Module"
        ),
        new OA\Property(
            property: "status",
            type: "string",
            nullable: true,
            description: "Status of the module (e.g., enabled or disabled)",
            example: "enabled"
        ),
        new OA\Property(
            property: "type",
            type: "string",
            nullable: true,
            description: "Type of module (e.g., core, custom)",
            example: "core"
        ),
        new OA\Property(
            property: "plan",
            type: "string",
            nullable: true,
            description: "Plan name associated with the module (if any)",
            example: "premium"
        ),
        new OA\Property(
            property: "description",
            type: "string",
            nullable: true,
            description: "Description of the module",
            example: "Provides blog functionality with categories and posts"
        ),
        new OA\Property(
            property: "images",
            type: "array",
            description: "Associated images for the module",
            items: new OA\Items(type: "string", format: "uri"),
            example: ["/uploads/modules/blog.png"]
        ),
        new OA\Property(
            property: "created_at",
            type: "string",
            format: "date-time",
            description: "Timestamp when the module was created",
            example: "2025-05-01T12:00:00Z"
        ),
        new OA\Property(
            property: "updated_at",
            type: "string",
            format: "date-time",
            description: "Timestamp when the module was last updated",
            example: "2025-05-02T09:30:00Z"
        )
    ]
)]
abstract class ModulesManagerAction extends Action
{
    protected ModuleRegistry $moduleRegistry;
    protected ModulesManagerRepositoryInterface $modulesManagerRepository;

    protected function afterConstruct(): void
    {
        $this->moduleRegistry = $this->container->get(ModuleRegistry::class);
        $this->modulesManagerRepository = $this->container->get(ModulesManagerRepositoryInterface::class);
    }
}
