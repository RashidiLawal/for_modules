<?php
declare(strict_types=1);

use BitCore\Kernel\App;

use Modules\Todo\Actions\Todos\{
    CreateTodoAction,
    DeleteTodoAction,
    ListAllTodosAction,
    ToggleCompleteAction,
    UpdateTodoAction,
    GetSingleTodoAction,
    DeleteBulkTodosAction,
    ListTodoBySlugAction,
};
use Modules\Todo\Actions\Groups\{
    GroupAction,
    DeleteBulkGroupsAction,
    DeleteGroupAction,
    GetSingleGroupAction,
    UpdateGroupAction,
    ListGroupBySlugAction,
    ListAllGroupsAction,
    
};

return function (App $app) {
    // Todos routes
    $app->group('/api/todos', function ($group) {
        $group->post('', CreateTodoAction::class)
            ->setName('todos.store');

        $group->get('', ListAllTodosAction::class)
            ->setName('todos.index');

        $group->get('/{id}', GetSingleTodoAction::class)
            ->setName('todos.show');

        $group->put('/{id}', UpdateTodoAction::class)
            ->setName('todos.update');

        $group->patch('/{id}', ToggleCompleteAction::class)
    ->setName('todos.update');

        $group->delete('/bulk', DeleteBulkTodosAction::class)
            ->setName('todos.bulkDelete');

        $group->delete('/{id}', DeleteTodoAction::class)
            ->setName('todos.delete');

        $group->get('/slug/{todo_slug}', ListTodoBySlugAction::class)
            ->setName('todos.fetchBySlug');
    });

    // Groups routes
    $app->group('/api/groups', function ($group) {
        $group->post('', GroupAction::class)
            ->setName('groups.store');

        $group->get('', ListAllGroupsAction::class)
            ->setName('groups.index');

        $group->get('/{id}', GetSingleGroupAction::class)
            ->setName('groups.show');

        $group->put('/{id}', UpdateGroupAction::class)
            ->setName('groups.update');

        $group->delete('/bulk', DeleteBulkGroupsAction::class)
            ->setName('groups.bulkDelete');

        $group->delete('/{id}', DeleteGroupAction::class)
            ->setName('groups.delete');

        $group->get('/slug/{group_slug}', ListGroupBySlugAction::class)
            ->setName('groups.fetchBySlug');
    });
};
