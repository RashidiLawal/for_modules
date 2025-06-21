<?php

declare(strict_types=1);

namespace Modules\Affiliate\Actions\Affiliates;

use Psr\Http\Message\ResponseInterface as Response;
use Modules\Affiliate\Models\Affiliate;
use Modules\Affiliate\Requests\Affiliates\CreateAffiliateRequest;
use OpenApi\Attributes as OA;

/**
 * Class CreateAffiliateAction
 *
 * Handles the creation of a new Affiliate.
 */
class CreateAffiliateAction extends AffiliateAction
{
    #[OA\Post(
        path: "[ROUTE:affiliates.store]",
        summary: "Create a new affiliate",
        description: "Creates a new affiliate with the provided data.",
        tags: ["Affiliate Module"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                required: ["affiliate_name", "affiliate_slug", "status"],
                properties: [
                    new OA\Property(property: "affiliate_name", type: "string", example: "John Doe"),
                    new OA\Property(property: "affiliate_slug", type: "string", example: "john-doe"),
                    new OA\Property(
                        property: "referral_link",
                        type: "string",
                        nullable: true,
                        example: "https://example.com/ref/john"
                    ),
                    new OA\Property(property: "status", type: "string", example: "enabled"),
                    new OA\Property(property: "clicks_generated", type: "integer", example: 0),
                    new OA\Property(property: "earnings", type: "number", format: "float", example: 0.00),
                    new OA\Property(
                        property: "payout_date",
                        type: "string",
                        format: "date",
                        example: "2025-05-01"
                    ),
                    new OA\Property(property: "payout_status", type: "string", example: "pending"),
                    new OA\Property(property: "total_sales", type: "integer", example: 0),
                    new OA\Property(property: "commission", type: "number", format: "float", example: 5.00),
                    new OA\Property(property: "group_id", type: "integer", nullable: true, example: 1)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Affiliate created successfully"),
            new OA\Response(response: 422, description: "Validation errors"),
            new OA\Response(response: 500, description: "Internal server error")
        ]
    )]
    protected function action(): Response
    {
        try {
            $data = CreateAffiliateRequest::data();

            // Validate input
            $errors = CreateAffiliateRequest::validate($data);
            if ($errors) {
                return $this->respondWithData([
                    'status' => false,
                    'errors' => $errors->all(),
                ], 422);
            }

            // Check for duplicate name
            if ($this->affiliateRepository->nameExists($data['affiliate_name'])) {
                return $this->respondWithData([
                    'status' => false,
                    'errors' => [trans("Affiliate::messages.affiliate_name_exists")],
                ], 409);
            }

            // Check for duplicate slug
            if (!empty($data['affiliate_slug']) && $this->affiliateRepository->findBySlug($data['affiliate_slug'])) {
                return $this->respondWithData([
                    'status' => false,
                    'errors' => [trans("Affiliate::messages.affiliate_slug_exists")],
                ], 409);
            }

            // Create affiliate
            $affiliate = $this->affiliateRepository->create($data);

            if (empty($affiliate->id)) {
                return $this->respondWithData([
                    'status' => false,
                    'message' => trans("Affiliate::messages.affiliate_creation_failed"),
                ], 400);
            }

            return $this->respondWithData([
                'status'  => true,
                'message' => trans("Affiliate::messages.affiliate_created"),
                'affiliate'    => $affiliate,
            ], 201);
        } catch (\Exception $e) {
            $this->logger->error("Affiliate creation failed: " . $e->getMessage(), [
                'exception' => $e
            ]);

            return $this->respondWithData([
                'status'  => false,
                'error'   => $e->getMessage(),
                'message' => trans("Affiliate::messages.unexpected_error"),
            ], 500);
        }
    }
}
