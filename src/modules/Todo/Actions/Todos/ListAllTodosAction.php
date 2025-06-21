<?php
declare(strict_types=1);

namespace Modules\Todo\Actions\Todos;


use Psr\Http\Message\ResponseInterface as Response;
use Modules\Todo\Requests\Todos\ListAllTodosRequest;

 class ListAllTodosAction extends TodoAction
 {
     protected function action(): Response
    {
         return $this->respondWithData([
                'status' => true,
                'message' => trans("Todo::messages.fetch_success"),
                'todos' => 'hello world'
            ]);
        // try {
        //     $queryParams = ListAllTodosRequest::data();

        //     $affiliates = $this->todoRepository->fetchAffiliates(
        //         $queryParams
        //     );

        //     return $this->respondWithData([
        //         'status' => true,
        //         'message' => trans("Todo::messages.fetch_success"),
        //         'todos' => $affiliates
        //     ]);
        // } catch (\Exception $e) {
        //     $this->logger->error("Failed to fetch todos: " . $e->getMessage());

        //     return $this->respondWithData([
        //         'status' => false,
        //         'error' => $e->getMessage(),
        //         'message' => trans("Todo::messages.fetch_failed"),
        //     ], 500);
        // }
    }

 }
 