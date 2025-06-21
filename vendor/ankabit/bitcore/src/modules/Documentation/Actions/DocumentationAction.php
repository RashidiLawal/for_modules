<?php

declare(strict_types=1);

namespace BitCore\Modules\Documentation\Actions;

use BitCore\Application\Actions\Action;
use BitCore\Modules\Documentation\Services\OpenApiPlaceholderReplacer;

abstract class DocumentationAction extends Action
{
    public OpenApiPlaceholderReplacer $openApiPlaceholderReplacer;

    protected function afterConstruct(): void
    {
        $this->openApiPlaceholderReplacer = $this->container->get(OpenApiPlaceholderReplacer::class);
    }
}
