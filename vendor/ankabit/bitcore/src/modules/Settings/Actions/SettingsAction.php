<?php

declare(strict_types=1);

namespace BitCore\Modules\Settings\Actions;

use BitCore\Application\Actions\Action;
use BitCore\Modules\Settings\Repositories\SettingsRepository;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Settings Module",
    description: "Application settings.
    \n\n**Postman Collection:** 
    [Download the Settings Module Collection](/api/postman?name=Settings)"
)]
abstract class SettingsAction extends Action
{
    protected SettingsRepository $settingsRepository;

    protected function afterConstruct(): void
    {
        $this->settingsRepository = $this->container->get(SettingsRepository::class);
    }
}
