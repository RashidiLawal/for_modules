<?php

declare(strict_types=1);

use BitCore\Kernel\App;
use BitCore\Modules\ModulesManager\Actions\ActivateModuleAction;
use BitCore\Modules\ModulesManager\Actions\BulkDeleteModulesAction;
use BitCore\Modules\ModulesManager\Actions\CreateModuleAction;
use BitCore\Modules\ModulesManager\Actions\DeactivateModuleAction;
use BitCore\Modules\ModulesManager\Actions\DeleteModuleAction;
use BitCore\Modules\ModulesManager\Actions\GetModuleAction;
use BitCore\Modules\ModulesManager\Actions\ListModulesAction;
use BitCore\Modules\ModulesManager\Actions\UpdateModuleAction;

return function (App $app) {
    // Modules routes
    $app->group('/api/modules', function ($group) {

        // Bulk delete modules
        $group->delete('/bulk', BulkDeleteModulesAction::class)
            ->setName('modules.bulkDelete');

        // Activate a module by name
        $group->post('/{name}/activate', ActivateModuleAction::class)
            ->setName('modules.activate');


        // Get a single module by name
        $group->get('/{name}', GetModuleAction::class)
            ->setName('modules.show');

        // Update a module by name
        $group->put('/{name}', UpdateModuleAction::class)
            ->setName('modules.update');

        // Delete a module by name
        $group->delete('/{name}', DeleteModuleAction::class)
            ->setName('modules.delete');


        // Deactivate a module by name
        $group->post('/{name}/deactivate', DeactivateModuleAction::class)
            ->setName('modules.deactivate');

        // List all modules
        $group->get('', ListModulesAction::class)
            ->setName('modules.index');

        // Create a new module
        $group->post('', CreateModuleAction::class)
            ->setName('modules.store');
    });
};
