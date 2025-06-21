<?php

declare(strict_types=1);

namespace Modules\Todo\Actions\Todos;

use Exception;
use Modules\Todo\Requests\Todos\UpdateTodoRequest;
use Psr\Http\Message\ResponseInterface as Response;

class UpdateTodoAction extends TodoAction 
{
     /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        try {
            $id = (int) $this->resolveArg('id');
            $data = UpdateTodoRequest::data();

            // Validate input
            $errors = UpdateTodoRequest::validate($data);
            if ($errors) {
                return $this->respondWithData([
                    'status' => false,
                    'errors' => $errors->all(),
                ], 422);
            }

            // Update todo
            $updated = $this->todoRepository->update($id, $data);

            if (empty($updated->id)) {
                return $this->respondWithData([
                    'status' => false,
                    'message' => trans("Todo::messages.todo_update_failed"),
                ], 400);
            }

            return $this->respondWithData([
                'status' => true,
                'message' => trans("Todo::messages.todo_updated"),
                'data' => $updated,
            ]);
        } catch (Exception $e) {
            $this->logger->error("Todo update failed: " . $e->getMessage(), [
                'exception' => $e,
            ]);

            return $this->respondWithData([
                'status' => false,
                'error' => $e->getMessage(),
                'message' => trans("Todo::messages.unexpected_error"),
            ], 500);
        }
    }

}