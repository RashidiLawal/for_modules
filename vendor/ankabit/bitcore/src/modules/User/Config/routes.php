<?php

declare(strict_types=1);

use BitCore\Modules\User\Actions\CreateUserAction;
use BitCore\Modules\User\Actions\ListUsersAction;
use BitCore\Modules\User\Actions\ViewUserAction;
use BitCore\Kernel\App;
use BitCore\Modules\User\Actions\TestUserAction;

return function (App $app) {
    $app->group('/users', function ($group) {
        $group->get('', ListUsersAction::class);
        $group->post('', CreateUserAction::class);
        $group->get('/create', CreateUserAction::class);
        $group->get('/test', TestUserAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });
};
