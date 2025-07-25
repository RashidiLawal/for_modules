<?php

declare(strict_types=1);

namespace BitCore\Modules\ModulesManager\Actions;

use Psr\Http\Message\ResponseInterface as Response;
use OpenApi\Attributes as OA;

/**
 * Class ActivateModuleAction
 *
 * Activates a module by its name.
 */
#[OA\Get(
    path: "[ROUTE:modules.activate]",
    summary: "Activate a module",
    description: "Activates a specific module by name.",
    tags: ["Modules Manager"],
    parameters: [
        new OA\Parameter(
            name: "name",
            in: "path",
            required: true,
            description: "The name of the module to activate",
            schema: new OA\Schema(type: "string", example: "Finance")
        ),
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: "Module activated successfully",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "status", type: "boolean", example: true),
                    new OA\Property(property: "message", type: "string", example: "Module activated successfully")
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
class ActivateModuleAction extends ModulesManagerAction
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

        // Run activation on the module
        $this->modulesManagerRepository->activate($module);

        return $this->respondWithData([
            'status'  => true,
            'module' => $module,
            'message' => trans("ModulesManager::messages.module_activated"),
        ], 200);
    }
}
