<?php

declare(strict_types=1);

namespace Modules\Affiliate\Actions\Affiliates;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Modules\Affiliate\Requests\Affiliates\UpdateAffiliateRequest;
use OpenApi\Attributes as OA;

/**
 * Class EditAffiliateAction
 *
 * Updates an existing affiliate.
 */
class EditAffiliateAction extends AffiliateAction
{
    /**
     * Handle the update request for an affiliate.
     *
     * @return Response JSON response with updated affiliate or error.
     */
    #[OA\Put(
        path: "[ROUTE:affiliates.update]",
        summary: "Update an existing affiliate",
        description: "Updates an affiliate with the provided data by ID.",
        tags: ["Affiliate Module"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "The ID of the affiliate to update",
                schema: new OA\Schema(type: "integer", example: 1)
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["affiliate_name", "affiliate_slug", "status"],
                type: "object",
                properties: [
                    new OA\Property(property: "affiliate_name", type: "string", example: "Jane Doe"),
                    new OA\Property(property: "affiliate_slug", type: "string", example: "jane-doe"),
                    new OA\Property(
                        property: "referral_link",
                        type: "string",
                        nullable: true,
                        example: "https://example.com/ref/jane"
                    ),
                    new OA\Property(property: "status", type: "string", example: "active"),
                    new OA\Property(property: "clicks_generated", type: "integer", example: 12),
                    new OA\Property(property: "earnings", type: "number", format: "float", example: 102.50),
                    new OA\Property(property: "payout_date", type: "string", format: "date", example: "2025-06-01"),
                    new OA\Property(property: "payout_status", type: "string", example: "paid"),
                    new OA\Property(property: "total_sales", type: "integer", example: 20),
                    new OA\Property(property: "commission", type: "number", format: "float", example: 7.50),
                    new OA\Property(property: "group_id", type: "integer", nullable: true, example: 2),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Affiliate updated successfully."),
            new OA\Response(response: 422, description: "Validation failed."),
            new OA\Response(response: 500, description: "Server error."),
        ]
    )]
    protected function action(): Response
    {
        try {
            $id = (int) $this->resolveArg('id');
            $data = UpdateAffiliateRequest::data();

            // Validate input
            $errors = UpdateAffiliateRequest::validate($data);
            if ($errors) {
                return $this->respondWithData([
                    'status' => false,
                    'errors' => $errors->all(),
                ], 422);
            }

            // Update affiliate
            $updated = $this->affiliateRepository->update($id, $data);

            if (empty($updated->id)) {
                return $this->respondWithData([
                    'status' => false,
                    'message' => trans("Affiliate::messages.affiliate_update_failed"),
                ], 400);
            }

            return $this->respondWithData([
                'status' => true,
                'message' => trans("Affiliate::messages.affiliate_updated"),
                'data' => $updated,
            ]);
        } catch (Exception $e) {
            $this->logger->error("Affiliate update failed: " . $e->getMessage(), [
                'exception' => $e,
            ]);

            return $this->respondWithData([
                'status' => false,
                'error' => $e->getMessage(),
                'message' => trans("Affiliate::messages.unexpected_error"),
            ], 500);
        }
    }
}
