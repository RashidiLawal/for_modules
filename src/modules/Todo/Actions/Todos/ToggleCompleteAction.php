<?php
declare(strict_types=1);

namespace Modules\Todo\Actions\Todos;

use Modules\Todo\Actions\Todos\TodoAction;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Class ToggleCompleteAction
 *
 * Handles wether the todo is completed or not.
 */
class ToggleCompleteAction extends TodoAction
{
    /**
     * {@inheritdoc}
     */
    public function action(): Response
    {
        // Get the todo ID from route parameters
         $id = (int) $this->resolveArg('id');

          
        // Find the todo by id
        $todo = $this->todoRepository->findById($id);
       
        // Toggle completion status
        $todo->setCompleted(true);
        $todo->save();
        
        $this->logger->info("Todo item {$id} completion status toggled to " . 
                          ($todo->isCompleted() ? true : false));
        
        return $this->respondWithData([
            'message' => 'Todo completion status updated successfully',
            'data' => [
                'id' => $todo->id,
                'completed' => $todo->isCompleted(),
                'title' => $todo->title
            ]
        ]);
    }
}