<?php

declare(strict_types=1);

namespace Modules\Todo\Actions\Todos;

use BitCore\Application\Actions\Action;
use Modules\Todo\Repositories\Todos\TodoRepositoryInterface;

abstract class TodoAction extends Action
{
    protected TodoRepositoryInterface $todoRepository;
    protected function afterConstruct(): void
    {
        $this->todoRepository = $this->container->get(TodoRepositoryInterface::class);
    }
}