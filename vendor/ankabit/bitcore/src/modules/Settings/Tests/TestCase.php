<?php

declare(strict_types=1);

namespace BitCore\Modules\Settings\Tests;

use BitCore\Tests\TestCase as BaseTestCase;
use BitCore\Modules\Settings\Repositories\SettingsRepository;

class TestCase extends BaseTestCase
{
    protected SettingsRepository $settingsRepository;


    protected function setUp(): void
    {
        parent::setUp();
        $app = $this->getAppInstance();
        $this->settingsRepository = $app->getContainer()->get(SettingsRepository::class)->load();
    }
}
