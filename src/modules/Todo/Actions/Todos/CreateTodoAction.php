<?php
declare(strict_types=1);
namespace Modules\Todo\Actions\Todos;

use Psr\Http\Message\ResponseInterface as Response;
use Modules\Todo\Requests\Todos\CreateTodoRequest;
use Modules\Todo\Actions\Todos\TodoAction;

/**
 * Class CreateTodoAction
 *
 * Handles the creation of a new todo item.
 */

class CreateTodoAction extends TodoAction
{
     /**
     * {@inheritdoc}
     */
     protected function action(): Response
    {

        try {
             // Get data from request class instance
             $data = CreateTodoRequest::data();

             // Validate received data
            $errors = CreateTodoRequest::validate($data);
            if ($errors) {
                return $this->respondWithData([
                    'status' => false,
                    'errors' => $errors->all(),
                ], 422);
            }

             // Check for duplicate title
            if ($this->todoRepository->nameExists($data['todo_title'])) {
                return $this->respondWithData([
                    'status' => false,
                    'errors' => [trans("Todo::messages.todo_title_exists")],
                ], 409);
            }

             // Check for duplicate slug
            if (!empty($data['todo_slug']) && $this->todoRepository->findBySlug($data['todo_slug'])) {
                return $this->respondWithData([
                    'status' => false,
                    'errors' => [trans("Todo::messages.todo_slug_exists")],
                ], 409);
            }

              // Create todo
            $todo = $this->todoRepository->create($data);

            if (empty($todo->id)) {
                return $this->respondWithData([
                    'status' => false,
                    'message' => trans("Todo::messages.todo_creation_failed"),
                ], 400);
            }

            return $this->respondWithData([
                'status'  => true,
                'message' => trans("Todo::messages.todo_created"),
                'todo'    => $todo,
            ], 201);

        } catch (\Exception $e) {
           $this->logger->error("Todo creation failed: " . $e->getMessage(), [
                'exception' => $e
            ]);

            return $this->respondWithData([
                'status'  => false,
                'error'   => $e->getMessage(),
                'message' => "messages unexpected error",
            ], 500);
        }
       
    }
   
}


