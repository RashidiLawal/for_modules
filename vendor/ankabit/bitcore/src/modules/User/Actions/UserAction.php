<?php

declare(strict_types=1);

namespace BitCore\Modules\User\Actions;

use BitCore\Application\Actions\Action;
use BitCore\Modules\User\Repositories\UserRepository;

abstract class UserAction extends Action
{
    protected UserRepository $userRepository;

    protected function afterConstruct(): void
    {
        $this->userRepository = $this->container->get(UserRepository::class);
    }
}
