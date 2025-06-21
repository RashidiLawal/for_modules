<?php

use BitCore\Foundation\Container;
use BitCore\Modules\Documentation\Services\OpenApiPlaceholderReplacer;

return function (Container $container) {
    $container->singleton(OpenApiPlaceholderReplacer::class, OpenApiPlaceholderReplacer::class);
};
