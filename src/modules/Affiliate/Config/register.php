<?php

use BitCore\Foundation\Container;
use Modules\Affiliate\Repositories\Affiliates\AffiliateRepository;
use Modules\Affiliate\Repositories\Affiliates\AffiliateRepositoryInterface;
use Modules\Affiliate\Repositories\Groups\GroupRepository;
use Modules\Affiliate\Repositories\Groups\GroupRepositoryInterface;

return function (Container $container) {
    $container->singleton(AffiliateRepositoryInterface::class, AffiliateRepository::class);
    $container->singleton(GroupRepositoryInterface::class, GroupRepository::class);
};
