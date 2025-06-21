<?php
declare(strict_types=1);

namespace Modules\Todo\Actions\Todos;

use Psr\Http\Message\ResponseInterface as Response;

class DeleteTodoAction extends TodoAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $id = (int) $this->resolveArg('id');

        $todo = $this->todoRepository->findById($id);
        if (empty($todo->id)) {
            return $this->respondWithData([
                'status' => false,
                'message' => trans("Todo::messages.todo_not_found"),
            ], 404);
        }
        $delete = $this->todoRepository->delete($id);
        if ($delete) {
            return $this->respondWithData([
                'status' => true,
                'message' => trans("Todo::messages.todo_deleted"),
            ], 200);
        } else {
            return $this->respondWithData([
                'status' => true,
                'message' => trans("Todo::messages.todo_failed_to_delete"),
            ], 500);
        }
    }
}