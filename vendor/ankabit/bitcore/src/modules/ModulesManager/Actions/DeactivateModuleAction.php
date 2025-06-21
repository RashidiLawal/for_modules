<?php

declare(strict_types=1);

namespace BitCore\Modules\ModulesManager\Actions;

use Psr\Http\Message\ResponseInterface as Response;
use OpenApi\Attributes as OA;
use BitCore\Application\Services\Modules\ModuleInterface;

/**
 * Class DeactivateModuleAction
 *
 * Deactivates a module by its name.
 */
#[OA\Get(
    path: "[ROUTE:modules.deactivate]",
    summary: "Deactivate a module",
    description: "Deactivates a specific module by name.",
    tags: ["Modules Manager"],
    parameters: [
        new OA\Parameter(
            name: "name",
            in: "path",
            required: true,
            description: "The name of the module to deactivate",
            schema: new OA\Schema(type: "string", example: "Finance")
        ),
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: "Module deactivated successfully",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "status", type: "boolean", example: true),
                    new OA\Property(property: "message", type: "string", example: "Module deactivated successfully")
                ]
            )
        ),
        new OA\Response(
            response: 404,
            description: "Module not found",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "status", type: "boolean", example: false),
                    new OA\Property(property: "message", type: "string", example: "Module not found")
                ]
            )
        ),
    ]
)]
class DeactivateModuleAction extends ModulesManagerAction
{
    protected function action(): Response
    {
        $name = $this->resolveArg('name');

        $module = $this->modulesManagerRepository->findByName($name);
        if (!$module) {
            return $this->respondWithData([
                'status' => false,
                'module' => $module,
                'message' => trans("ModulesManager::messages.module_not_found"),
            ], 404);
        }

        if ($module->isCore()) {
            return $this->respondWithData([
                'status' => false,
                'module' => $module,
                'message' => trans("ModulesManager::messages.action_not_allow_on_core_module"),
            ], 403);
        }

        $this->modulesManagerRepository->deactivate($module);

        $updated = $this->modulesManagerRepository->update($module->id, ['status' => 'inactive']);

        return $this->respondWithData([
            'status'  => true,
            'module' => $updated,
            'message' => trans("ModulesManager::messages.module_deactivated"),
        ], 200);
    }
}
