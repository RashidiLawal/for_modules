<?php

declare(strict_types=1);

namespace BitCore\Modules\ModulesManager\Actions;

use Psr\Http\Message\ResponseInterface as Response;
use BitCore\Modules\ModulesManager\Requests\UploadModuleRequest;
use OpenApi\Attributes as OA;

/**
 * Class UploadModuleAction
 *
 * Handles uploading of a module (ZIP or JSON file).
 */
class UploadModuleAction extends ModulesManagerAction
{
    /**
     * Upload a module file.
     *
     * @return Response JSON response indicating success or failure.
     */
    #[OA\Post(
        path: "[ROUTE:modules.upload]",
        summary: "Upload a module",
        description: "Uploads a module in ZIP or JSON format.",
        tags: ["Modules Manager"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    type: "object",
                    required: ["file"],
                    properties: [
                        new OA\Property(
                            property: "file",
                            type: "string",
                            format: "binary",
                            description: "ZIP or JSON module file"
                        )
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Module uploaded successfully",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Module uploaded successfully")
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Invalid file format",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: false),
                        new OA\Property(property: "errors", type: "array", items: new OA\Items(type: "string"))
                    ]
                )
            )
        ]
    )]
    protected function action(): Response
    {
        $file = $this->request->getUploadedFiles()['file'] ?? null;

        $errors = UploadModuleRequest::validate($file);
        if ($errors) {
            return $this->respondWithData([
                'status' => false,
                'errors' => $errors->all(),
            ], 422);
        }

        // Save or extract logic here (skipped for brevity)

        return $this->respondWithData([
            'status' => true,
            'message' => trans("ModulesManager::messages.module_uploaded"),
        ], 200);
    }
}
