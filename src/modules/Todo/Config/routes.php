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
    $app->group('/api/todos_group', function ($group) {
        $group->post('', GroupAction::class)
            ->setName('todo_groups.store');

        $group->get('', ListAllGroupsAction::class)
            ->setName('todo_groups.index');

        $group->get('/{id}', GetSingleGroupAction::class)
            ->setName('todo_groups.show');

        $group->put('/{id}', UpdateGroupAction::class)
            ->setName('todo_groups.update');

        $group->delete('/bulk', DeleteBulkGroupsAction::class)
            ->setName('todo_groups.bulkDelete');

        $group->delete('/{id}', DeleteGroupAction::class)
            ->setName('todo_groups.delete');

        $group->get('/slug/{group_slug}', ListGroupBySlugAction::class)
            ->setName('todo_groups.fetchBySlug');
    });
};
