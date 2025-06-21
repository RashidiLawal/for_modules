<?php

declare(strict_types=1);

namespace BitCore\Application\Middleware;

use BitCore\Application\Events\CsrfExcludedPaths;
use BitCore\Application\Exception\HttpException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Csrf\Guard;

/**
 * Middleware that handle csrf protection for all request in the app.
 */
class CsrfMiddleware extends Guard
{
    /**
     * @param  Request  $request
     * @param  RequestHandler $handler
     *
     * @throws HttpException
     */
    public function gracefulFailureHandler(Request $request, RequestHandler $handler): Response
    {
        // Throw exception to inherit the existing robust error handler.
        throw new HttpException($request, trans('auth.csrf'), 400);
    }

    /**
     * {@inheritdoc}
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        // Dispatch the event to allow other parts of the application to modify the excluded paths
        $csrfSettings = (array)config()->get('csrf');

        $excludedPaths = $csrfSettings['excluded_paths'] ?? [];
        $excludedPaths[] = $csrfSettings['route'];

        $event = new CsrfExcludedPaths($excludedPaths);
        hooks()->dispatch(CsrfExcludedPaths::class, $event);

        // Get the current URI from the PSR-7 request
        $currentUri = $request->getUri()->getPath();

        // Skip CSRF middleware if the URI is in the excluded paths
        if (in_array($currentUri, $event->paths, true)) {
            return $handler->handle($request);
        }

        $this->setFailureHandler([$this, 'gracefulFailureHandler']);

        return parent::process($request, $handler);
    }
}
