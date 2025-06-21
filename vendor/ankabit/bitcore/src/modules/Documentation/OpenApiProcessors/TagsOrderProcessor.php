<?php

declare(strict_types=1);

namespace BitCore\Modules\Documentation\OpenApiProcessors;

use OpenApi\Analysis;

class TagsOrderProcessor
{
    protected array $priorityMap;

    public function __construct(array $priorityMap = [])
    {
        $this->priorityMap = $priorityMap;
    }

    public function __invoke(Analysis $analysis)
    {
        $openapi = $analysis->openapi;

        if (!$openapi || empty($openapi->tags)) {
            return;
        }

        // Sort tags by priority map, fallback to alphabetical
        usort($openapi->tags, function ($a, $b) {
            $aName = $a->name;
            $bName = $b->name;

            $aPriority = $this->getPriority($aName);
            $bPriority = $this->getPriority($bName);

            if ($aPriority === $bPriority) {
                return strcasecmp($aName, $bName);
            }

            return $bPriority - $aPriority;
        });
    }

    protected function getPriority(string $tagName): int
    {
        foreach ($this->priorityMap as $prefix => $priority) {
            if (str_starts_with($tagName, $prefix)) {
                return $priority;
            }
        }

        // fallback priority if no match — set low so it comes later
        return 1;
    }
}
