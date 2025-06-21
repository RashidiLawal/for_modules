<?php

declare(strict_types=1);

namespace Modules\Affiliate\Actions\Affiliates;

use Psr\Http\Message\ResponseInterface as Response;
use OpenApi\Attributes as OA;

/**
 * Delete an Affiliate.
 */
class DeleteAffiliateAction extends AffiliateAction
{
    #[OA\Delete(
        path: "[ROUTE:affiliates.delete]",
        summary: "Delete an affiliate",
        description: "Remove an affiliate by ID.",
        tags: ["Affiliate Module"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer"),
                description: "The ID of the affiliate to delete"
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Affiliate deleted successfully"),
            new OA\Response(response: 404, description: "Affiliate not found"),
            new OA\Response(response: 500, description: "Server error")
        ]
    )]
    protected function action(): Response
    {
        $id = (int) $this->resolveArg('id');

        $affiliate = $this->affiliateRepository->findById($id);
        if (empty($affiliate->id)) {
            return $this->respondWithData([
                'status' => false,
                'message' => trans("Affiliate::messages.affiliate_not_found"),
            ], 404);
        }
        $delete = $this->affiliateRepository->delete($id);
        if ($delete) {
            return $this->respondWithData([
                'status' => true,
                'message' => trans("Affiliate::messages.affiliate_deleted"),
            ], 200);
        } else {
            return $this->respondWithData([
                'status' => true,
                'message' => trans("Affiliate::messages.affiliate_failed_to_delete"),
            ], 500);
        }
    }
}
