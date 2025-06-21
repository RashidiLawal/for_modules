<?php

declare(strict_types=1);

use BitCore\Modules\Documentation\Actions\GenerateApiDocumentation;
use BitCore\Modules\Documentation\Actions\GenerateApiDocumentationUi;
use BitCore\Modules\Documentation\Actions\GeneratePostmanCollection;
use BitCore\Kernel\App;

return function (App $app) {
    $app->group('/api/docs', function ($group) {
        $group->get('/json', GenerateApiDocumentation::class);
        $group->get('/ui', GenerateApiDocumentationUi::class);
        $group->get('/postman', GeneratePostmanCollection::class);
    });
};
