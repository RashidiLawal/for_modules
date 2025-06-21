<?php

use BitCore\Application\Services\Modules\ModuleRegistry;
use BitCore\Foundation\Container;
use BitCore\Modules\ModulesManager\Repositories\ModulesManagerRepository;
use BitCore\Modules\ModulesManager\Repositories\ModulesManagerRepositoryInterface;

return function (Container $container) {
    $container->singleton(ModulesManagerRepositoryInterface::class, function () use ($container) {
        return new ModulesManagerRepository($container->get(ModuleRegistry::class));
    });
};
