<?php

declare(strict_types=1);

namespace BitCore\Application\Jobs;

interface JobInterface
{
    public function handle();
}
