<?php

declare(strict_types=1);

namespace Modules\Todo\Actions\Todos;

use Psr\Http\Message\ResponseInterface as Response;
use OpenApi\Attributes as OA;

/**
 * Class ListTodoBySlugAction
 *
 * Handles listing a todo by its slug.
 */
class ListTodoBySlugAction extends TodoAction
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
                description: "Todo not found"
            )
        ]
    )]
    protected function action(): Response
    {
        $slug = $this->resolveArg('todo_slug');

        $todo = $this->todoRepository->findBySlug($slug);

        if (empty($todo->id)) {
            return $this->respondWithData([
                'status' => false,
                'message' => trans('Todo::messages.todo_not_found'),
            ], 404);
        }

        return $this->respondWithData([
            'status' => true,
            'message' => trans("Todo::messages.todo_fetched"),
            'data' => $todo,
        ], 200);
    }
}
