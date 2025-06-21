<?php

declare(strict_types=1);

use BitCore\Kernel\App;
use Modules\Affiliate\Actions\Affiliates\CreateAffiliateAction;
use Modules\Affiliate\Actions\Affiliates\DeleteAffiliateAction;
use Modules\Affiliate\Actions\Affiliates\DeleteBulkAffiliatesAction;
use Modules\Affiliate\Actions\Affiliates\EditAffiliateAction;
use Modules\Affiliate\Actions\Affiliates\FetchAffiliateBySlugAction;
use Modules\Affiliate\Actions\Affiliates\FetchAllAffiliatesAction;
use Modules\Affiliate\Actions\Affiliates\GetSingleAffiliateAction;
use Modules\Affiliate\Actions\Groups\CreateGroupAction;
use Modules\Affiliate\Actions\Groups\DeleteBulkGroupsAction;
use Modules\Affiliate\Actions\Groups\DeleteGroupAction;
use Modules\Affiliate\Actions\Groups\EditGroupAction;
use Modules\Affiliate\Actions\Groups\FetchAllGroupsAction;
use Modules\Affiliate\Actions\Groups\FetchGroupBySlugAction;
use Modules\Affiliate\Actions\Groups\GetSingleGroupAction;

return function (App $app) {
    // Affiliates routes
    $app->group('/api/affiliates', function ($group) {
        $group->post('', CreateAffiliateAction::class)
            ->setName('affiliates.store');

        $group->get('', FetchAllAffiliatesAction::class)
            ->setName('affiliates.index');

              $group->get('/hello', 'Bornfnfnndf')
            ->setName('todos.index');

        $group->get('/{id}', GetSingleAffiliateAction::class)
            ->setName('affiliates.show');

        $group->put('/{id}', EditAffiliateAction::class)
            ->setName('affiliates.update');

        $group->delete('/bulk', DeleteBulkAffiliatesAction::class)
            ->setName('affiliates.bulkDelete');

        $group->delete('/{id}', DeleteAffiliateAction::class)
            ->setName('affiliates.delete');

        $group->get('/slug/{affiliate_slug}', FetchAffiliateBySlugAction::class)
            ->setName('affiliates.fetchBySlug');
    });

    // Groups routes
    $app->group('/api/groups', function ($group) {
        $group->post('', CreateGroupAction::class)
            ->setName('groups.store');

        $group->get('', FetchAllGroupsAction::class)
            ->setName('groups.index');

        $group->get('/{id}', GetSingleGroupAction::class)
            ->setName('groups.show');

        $group->put('/{id}', EditGroupAction::class)
            ->setName('groups.update');

        $group->delete('/bulk', DeleteBulkGroupsAction::class)
            ->setName('groups.bulkDelete');

        $group->delete('/{id}', DeleteGroupAction::class)
            ->setName('groups.delete');

        $group->get('/slug/{group_slug}', FetchGroupBySlugAction::class)
            ->setName('groups.fetchBySlug');
    });
};
