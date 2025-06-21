<?php

declare(strict_types=1);

namespace Modules\Todo\Actions\Groups;

use Psr\Http\Message\ResponseInterface as Response;
use OpenApi\Attributes as OA;

/**
 * Class FetchGroupBySlugAction
 *
 * Handles retrieving an affiliate group by its slug.
 */
class ListGroupBySlugAction extends GroupAction
{
    #[OA\Get(
        path: "[ROUTE:groups.fetchBySlug]",
        summary: "Fetch an affiliate group by slug",
        description: "Retrieve a single affiliate group using its slug.",
        tags: ["Affiliate Module"],
        parameters: [
            new OA\Parameter(
                name: "slug",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string"),
                description: "Slug of the affiliate group"
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Affiliate group found",
                content: new OA\JsonContent(ref: "#/components/schemas/AffiliateGroup")
            ),
            new OA\Response(
                response: 404,
                description: "Affiliate group not found"
            )
        ]
    )]
    protected function action(): Response
    {
        $slug = $this->resolveArg('group_slug');

        // Find the group by its slug
        $group = $this->groupRepository->findBySlug($slug);

        if (empty($group->id)) {
            return $this->respondWithData([
                'status' => false,
                'message' => trans('Affiliate::messages.group_not_found'),
            ], 404);
        }

        return $this->respondWithData([
            'status' => true,
            'message' => trans("Affiliate::messages.group_fetched"),
            'data' => $group,
        ], 200);
    }
}
