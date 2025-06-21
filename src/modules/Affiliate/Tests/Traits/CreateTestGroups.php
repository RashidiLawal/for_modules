<?php

declare(strict_types=1);

namespace Modules\Affiliate\Tests\Traits;

use BitCore\Foundation\Carbon;
use Modules\Affiliate\Models\Group;

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
            'group_name'               => 'Test Group ' . uniqid(),
            'group_slug'               => 'test-group-' . uniqid(),
            'clicks_generated'         => 0,
            'total_earnings'           => 0.0,
            'is_auto_approved'         => true,
            'default_commission_rate'  => 5.0,

        ], $data));
    }
}
