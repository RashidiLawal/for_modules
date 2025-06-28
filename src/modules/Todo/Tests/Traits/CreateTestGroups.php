<?php

declare(strict_types=1);

namespace Modules\Todo\Tests\Traits;

use BitCore\Foundation\Carbon;
use Modules\Todo\Models\Group;

trait CreateTestGroups
{
    /**
     * Creates a test group record.
     *
     * @param array $data Custom group data to override defaults.
     * @return Group
     */
    public function createGroup(array $data = []): Group
    {
        return Group::create(array_merge([
            'group_title'               => 'Test Group ' . uniqid(),
            'group_description'        => 'Test for Group-' . uniqid(),
            'group_slug'               => 'test-group-' . uniqid(),
            'completed'                => true,
        ], $data));
    }
}
