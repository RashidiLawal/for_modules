<?php

declare(strict_types=1);

namespace Modules\Affiliate\Actions\Affiliates;

use Psr\Http\Message\ResponseInterface as Response;
use Modules\Affiliate\Requests\Affiliates\FetchAllAffiliatesRequest;
use OpenApi\Attributes as OA;

/**
 * Class FetchAllAffiliatesAction
 *
 * Handles fetching and filtering affiliates.
 */
class FetchAllAffiliatesAction extends AffiliateAction
{
    #[OA\Get(
        path: "[ROUTE:affiliates.index]",
        summary: "Fetch all affiliates",
        description: "Retrieve a paginated list of affiliates with filters and sorting.",
        tags: ["Affiliate Module"],
        parameters: [
            new OA\Parameter(
                name: "search",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "string"),
                example: "john"
            ),
            new OA\Parameter(
                name: "status",
                in: "query",
                required: false,
                schema: new OA\Schema(
                    type: "string",
                    enum: ["active", "inactive", "pending"]
                )
            ),
            new OA\Parameter(
                name: "sort_by",
                in: "query",
                required: false,
                schema: new OA\Schema(
                    type: "string",
                    enum: ["affiliate_name", "earnings", "created_at"]
                )
            ),
            new OA\Parameter(
                name: "order",
                in: "query",
                required: false,
                schema: new OA\Schema(
                    type: "string",
                    enum: ["asc", "desc"]
                ),
                example: "asc"
            ),
            new OA\Parameter(
                name: "page",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "integer"),
                example: 1
            ),
            new OA\Parameter(
                name: "per_page",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "integer"),
                example: 10
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of affiliates",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: true),
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: "Affiliates fetched successfully."
                        ),
                        new OA\Property(
                            property: "affiliates",
                            type: "array",
                            items: new OA\Items(ref: "#/components/schemas/Affiliate")
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: "Unexpected error",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "An unexpected error occurred."),
                        new OA\Property(property: "error", type: "string")
                    ]
                )
            )
        ]
    )]
    protected function action(): Response
    {
        try {
            $queryParams = FetchAllAffiliatesRequest::data();

            $affiliates = $this->affiliateRepository->fetchAffiliates(
                $queryParams
            );

            return $this->respondWithData([
                'status' => true,
                'message' => trans("Affiliate::messages.fetch_success"),
                'affiliates' => $affiliates
            ]);
        } catch (\Exception $e) {
            $this->logger->error("Failed to fetch affiliates: " . $e->getMessage());

            return $this->respondWithData([
                'status' => false,
                'error' => $e->getMessage(),
                'message' => trans("Affiliate::messages.fetch_failed"),
            ], 500);
        }
    }
}
