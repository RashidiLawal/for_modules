<?php

use BitCore\Foundation\Container;
use Modules\Todo\Repositories\Todos\TodoRepository;
use Modules\Todo\Repositories\Todos\TodoRepositoryInterface;
use Modules\Todo\Repositories\Groups\GroupRepository;
use Modules\Todo\Repositories\Groups\GroupRepositoryInterface;

return function (Container $container) {
    $container->singleton(TodoRepositoryInterface::class, TodoRepository::class);
     $container->singleton(GroupRepositoryInterface::class, GroupRepository::class);
};
