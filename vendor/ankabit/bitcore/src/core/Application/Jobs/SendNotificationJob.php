<?php

declare(strict_types=1);

namespace BitCore\Application\Jobs;

class SendNotificationJob implements JobInterface
{
    public $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function handle()
    {
        echo "Processing Job: " . $this->message . "\n";
    }
}
