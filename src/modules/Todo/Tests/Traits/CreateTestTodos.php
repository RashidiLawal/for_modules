<?php

declare(strict_types=1);

namespace Modules\Todo\Tests\Traits;

use BitCore\Foundation\Carbon;
use Modules\Todo\Models\Todo;

trait CreateTestTodos
{
    /**
     * Creates a test affiliate record.
     *
     * @param array $data Custom affiliate data to override defaults.
     * @return Todo
     */
    public function createTodo(array $data = []): Todo
    {
        return Todo::create(array_merge([
            'todo_name'    => 'Test Affiliate ' . uniqid(),
            'todo_slug'    => 'test-affiliate-' . uniqid(),
            'referral_link'     => 'https://example.com/referral/' . uniqid(),
            'status'            => 'enabled',
            'clicks_generated'  => 0,
            'earnings'          => 0.0,
            'payout_date'       => null,
            'payout_status'     => 'pending',
            'total_sales'       => 0,
            'commission'        => 5.0,
            'group_id'          => 1, // Default group ID, adjust as needed
        ], $data));
    }
}
