<?php

declare(strict_types=1);

namespace BitCore\Modules\Documentation\OpenApiProcessors;

use OpenApi\Analysis;

class RoutePlaceholderProcessor
{
    protected array $routePlaceholders;

    public function __construct(array $routePlaceholders)
    {
        $this->routePlaceholders = $routePlaceholders;
    }
    /**
     * Replace placeholders in PathItem and Operation annotations.
     *
     * @param Analysis $analysis
     * @return void
     */
    public function __invoke(Analysis $analysis): void
    {
        foreach ($analysis->annotations as $annotation) {
            if (!isset($annotation->path)) {
                continue;
            }

            $this->replacePath($annotation);
        }
    }

    /**
     * Replace placeholders in the path property.
     *
     * @param object $annotation
     * @return void
     */
    protected function replacePath(object $annotation): void
    {
        if (isset($annotation->path) && is_string($annotation->path)) {
            $annotation->path = strtr($annotation->path, $this->routePlaceholders);
        }
    }
}
