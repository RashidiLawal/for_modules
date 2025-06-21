<?php

declare(strict_types=1);

namespace Modules\Affiliate\Actions\Affiliates;

use Psr\Http\Message\ResponseInterface as Response;
use OpenApi\Attributes as OA;

/**
 * Class DeleteBulkAffiliatesAction
 *
 * Handles the bulk deletion of affiliates.
 */
class DeleteBulkAffiliatesAction extends AffiliateAction
{
    #[OA\Delete(
        path: "[ROUTE:affiliates.bulkDelete]",
        summary: "Delete multiple affiliates",
        description: "Deletes multiple affiliates based on the provided array of affiliate IDs.",
        tags: ["Affiliate Module"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                required: ["ids"],
                properties: [
                    new OA\Property(
                        property: "ids",
                        type: "array",
                        items: new OA\Items(type: "integer"),
                        example: [1, 2, 3],
                        description: "Array of affiliate IDs to delete"
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Affiliates deleted successfully",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: true),
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: "Affiliates deleted successfully."
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Invalid input",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Invalid ID list")
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
                        new OA\Property(property: "message", type: "string", example: "Affiliate deletion failed")
                    ]
                )
            )
        ]
    )]
    /**
     * Execute the bulk affiliate deletion.
     *
     * @return Response JSON response indicating success or failure.
     */
    protected function action(): Response
    {
        $params = (array) $this->getFormData();

        if (!isset($params['ids']) || !is_array($params['ids'])) {
            return $this->respondWithData([
                'status' => false,
                'message' => trans("Affiliate::messages.invalid_id_list"),
            ], 422);
        }

        $deleted = $this->affiliateRepository->bulkDelete($params['ids']);

        return $this->respondWithData([
            'status' => $deleted ? true : false,
            'message' => $deleted
                ? trans("Affiliate::messages.bulk_affiliate_delete_success")
                : trans("Affiliate::messages.bulk_affiliate_delete_failed"),
        ], $deleted ? 200 : 500);
    }
}
