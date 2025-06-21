<?php

declare(strict_types=1);

namespace BitCore\Modules\Settings\Requests;

use BitCore\Application\Services\Requests\RequestValidator;

class SaveSettingsRequest extends RequestValidator
{
    /**
     * Get validation rules for saving multiple settings.
     */
    public static function rules(): array
    {
        return [
            'settings' => ['required', 'array']
        ];
    }
}
