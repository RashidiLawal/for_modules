<?php

declare(strict_types=1);

namespace BitCore\Modules\ModulesManager\Tests\Traits;

use BitCore\Modules\ModulesManager\Models\Module;

trait CreateTestModules
{
    /**
     * Creates a test module record.
     *
     * @param array $data Custom module data to override defaults.
     * @return Module
     */
    public function createModule(array $data = []): Module
    {
        return Module::create(array_merge([
            'name'        => 'TestModule_' . uniqid(),
            'priority'    => 1,
            'entry'       => 'Modules/TestModule/index.php',
            'status'      => 'inactive',
            'type'        => 'custom',
            'plan'        => 'free',
            'description' => 'This is a test module.',
            'images'      => json_encode(['icon.png']),
        ], $data));
    }
}
