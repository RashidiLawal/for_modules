<?php

use BitCore\Foundation\Container;
use BitCore\Modules\User\Repositories\UserRepository;
use BitCore\Modules\User\Repositories\UserRepositoryInterface;

return function (Container $container) {
    $container->singleton(UserRepositoryInterface::class, UserRepository::class);
};
