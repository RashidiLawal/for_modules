<?php

declare(strict_types=1);

use BitCore\Application\Actions\CsrfCookieAction;
use BitCore\Application\Actions\InstallAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use BitCore\Kernel\App;

return function (App $app) {

    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    //CSRF token
    $csrfSettings = (array)config()->get('csrf');
    if ($csrfSettings['enabled'] && !empty($csrfSettings['route'])) {
        $app->get($csrfSettings['route'], CsrfCookieAction::class);
    }

    //@todo Make separte installer controller and add gate check
    $app->get('/install', InstallAction::class);

    // Define routes
    $app->get('vvc/', function ($request, $response) {
        // @todo Remove sample code
        // Example of using Illuminate Filesystem
        /*$filesystem = $this->get('filesystem');
            $filesystem->put('example.txt', 'Hello, Illuminate Filesystem!');

            // Example of using Illuminate Events
            hooks()->dispatch('example.event', ['data' => 'Some data']);

            $response->getBody()->write('Illuminate packages example!');
            return $response;
            */
    });

    $app->get('/translate', function (Request $request, Response $response) {
        $response->getBody()->write(trans('messages.healthy'));
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write(trans('messages.healthy'));
        return $response;
    });
};
