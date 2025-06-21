<?php

declare(strict_types=1);

namespace BitCore\Modules\ModulesManager\Actions;

use Psr\Http\Message\ResponseInterface as Response;
use BitCore\Modules\ModulesManager\Requests\CreateModuleRequest;
use OpenApi\Attributes as OA;
use ZipArchive;

/**
 * Class CreateModuleAction
 *
 * Handles the creation of a new module.
 */
class CreateModuleAction extends ModulesManagerAction
{
    /**
     * Execute the module creation process.
     *
     * @return Response JSON response containing the created module details.
     */
    #[OA\Post(
        path: "[ROUTE:modules.store]",
        summary: "Create a new module",
        description: "Creates a new module with the provided metadata.",
        tags: ["Modules Manager"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                required: ["name", "namespace", "description", "version"],
                properties: [
                    new OA\Property(
                        property: "name",
                        type: "string",
                        example: "Blog",
                        description: "Name of the module"
                    ),
                    new OA\Property(
                        property: "namespace",
                        type: "string",
                        example: "Modules\\Blog",
                        description: "Namespace of the module"
                    ),
                    new OA\Property(
                        property: "description",
                        type: "string",
                        example: "A blogging module",
                        description: "Description of the module"
                    ),
                    new OA\Property(
                        property: "version",
                        type: "string",
                        example: "1.0.0",
                        description: "Version of the module"
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Module created successfully",
                content: new OA\JsonContent(ref: "#/components/schemas/ModulesManager")
            ),
            new OA\Response(
                response: 422,
                description: "Validation errors",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: false),
                        new OA\Property(property: "errors", type: "array", items: new OA\Items(type: "string"))
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
                        new OA\Property(property: "message", type: "string", example: "An unexpected error occurred.")
                    ]
                )
            )
        ]
    )]
    protected function action(): Response
    {
        try {
            $data = CreateModuleRequest::data();

            $errors = CreateModuleRequest::validate($data);
            if ($errors) {
                return $this->respondWithData([
                    'status' => false,
                    'errors' => $errors->all(),
                ], 422);
            }

            $storage = storage('local'); // local storage

            $file = is_string($data['file']) ? $data['file'] : $data['file']->store('tmp', 'local');

            $file = $storage->path($file);

            $createdModule = $this->modulesManagerRepository->install($file);
            if (!$createdModule) {
                return $this->respondWithData([
                    'status' => false,
                    'message' => trans("ModulesManager::messages.module_installation_failed"),
                ], 400);
            }

            $module = $this->modulesManagerRepository->create($data);
            if (empty($module->id)) {
                return $this->respondWithData([
                    'status' => false,
                    'message' => trans("ModulesManager::messages.module_creation_failed"),
                ], 400);
            }

            return $this->respondWithData([
                'status' => true,
                'message' => trans("ModulesManager::messages.module_created"),
                'data' => $module,
            ], 201);
        } catch (\Exception $e) {
            $this->logger->error("Module creation failed: " . $e->getMessage(), [
                'exception' => $e
            ]);

            return $this->respondWithData([
                'status' => false,
                'message' => trans("ModulesManager::messages.unexpected_error"),
            ], 500);
        }
    }
}
