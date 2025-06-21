<?php

declare(strict_types=1);

namespace Modules\Affiliate\Actions\Affiliates;

use Psr\Http\Message\ResponseInterface as Response;
use OpenApi\Attributes as OA;

/**
 * Class FetchAffiliateBySlugAction
 *
 * Handles retrieving an affiliate by its slug.
 */
class FetchAffiliateBySlugAction extends AffiliateAction
{
    #[OA\Get(
        path: "[ROUTE:affiliates.fetchBySlug]",
        summary: "Fetch an affiliate by slug",
        description: "Retrieve a single affiliate using its slug.",
        tags: ["Affiliate Module"],
        parameters: [
            new OA\Parameter(
                name: "affiliate_slug",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string"),
                description: "Slug of the affiliate"
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Affiliate found",
                content: new OA\JsonContent(ref: "#/components/schemas/Affiliate")
            ),
            new OA\Response(
                response: 404,
                description: "Affiliate not found"
            )
        ]
    )]
    protected function action(): Response
    {
        $slug = $this->resolveArg('affiliate_slug');

        $affiliate = $this->affiliateRepository->findBySlug($slug);

        if (empty($affiliate->id)) {
            return $this->respondWithData([
                'status' => false,
                'message' => trans('Affiliate::messages.affiliate_not_found'),
            ], 404);
        }

        return $this->respondWithData([
            'status' => true,
            'message' => trans("Affiliate::messages.affiliate_fetched"),
            'data' => $affiliate,
        ], 200);
    }
}
