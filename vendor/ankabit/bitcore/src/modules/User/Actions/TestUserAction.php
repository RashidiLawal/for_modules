<?php

declare(strict_types=1);

namespace BitCore\Modules\User\Actions;

use Psr\Http\Message\ResponseInterface as Response;

class TestUserAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        return $this->respondWithData([
            'message' => trans("messages.healthy"), // global message
            'message2' => trans("User::messages.title"), // local message from module
            'message3' => trans(
                "User::messages.healthy",
                ['name' => 'MeMyName']
            ), // local message from module with replacement
        ]);
    }
}
