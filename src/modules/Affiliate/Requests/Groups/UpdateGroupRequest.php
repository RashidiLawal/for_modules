<?php

declare(strict_types=1);

namespace Modules\Affiliate\Requests\Groups;

use BitCore\Application\Services\Requests\RequestValidator;

class UpdateGroupRequest extends RequestValidator
{
    /**
     * Define validation rules for updating an affiliate group.
     *
     * @return array
     */
    public static function rules(): array
    {
        return [
            'group_name'              => 'required|string|max:255',
            'group_slug'              => 'required|string|max:255|alpha_dash',
            'clicks_generated'        => 'nullable|integer|min:0',
            'total_earnings'          => 'nullable|numeric|min:0',
            'is_auto_approved'        => 'nullable|boolean',
            'default_commission_rate' => 'nullable|numeric|min:0',
            'commission_lock_period'  => 'nullable|integer|min:0',
            'reward_type'             => 'nullable|in:percentage,flat,custom',
            'is_enable_commission'    => 'nullable|boolean',
            'commission_rate'         => 'nullable|numeric|min:0',
            'commission_type'         => 'nullable|in:flat,percentage,custom',
            'commission_rule'         => 'nullable|string|max:255',
            'commission_amount'       => 'nullable|numeric|min:0',
            'payout_minimum'          => 'nullable|numeric|min:0',
        ];
    }
}
