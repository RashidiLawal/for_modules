<?php

declare(strict_types=1);

namespace BitCore\Tests;

use Exception;
use BitCore\Kernel\Bootstrap;
use PHPUnit\Framework\TestCase as PHPUnit_TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Nyholm\Psr7Server\ServerRequestCreator;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ServerRequestInterface as Request;
use BitCore\Kernel\App;

class TestCase extends PHPUnit_TestCase
{
    use ProphecyTrait;

    protected App|null $app = null;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a fresh instance of the app for each test
        $this->app = $this->getAppInstance();
    }

    protected function tearDown(): void
    {
        $this->app = null;
    }

    /**
     * Initializes and returns an application instance with database migrations.
     *
     * This method sets up the Slim application, configures the container, and ensures
     * database migrations are applied. If migration tables do not exist, they are created.
     *
     * @return App The initialized application instance.
     * @throws Exception If an error occurs during initialization.
     */
    protected function getAppInstance(): App
    {
        if ($this->app) {
            return $this->app;
        }

        // Bootstrap the application and get an instance
        $app = (new Bootstrap())->runTest();

        return $app;
    }

    /**
     * Creates a PSR-7 server request with optional parameters.
     *
     * This method generates a server request using Nyholm PSR-7 factories.
     * It allows specifying HTTP method, request path, POST data, files, headers, cookies, and server parameters.
     *
     * @param string $method        HTTP request method (GET, POST, etc.).
     * @param string $path          Request URI.
     * @param array|null $post      Optional POST data.
     * @param array|null $files          Optional uploaded files.
     * @param array|null $headers        Optional request headers.
     * @param array|null $cookies        Optional cookies.
     * @param array|null $serverParams   Optional server parameters.
     * @param array|null $queryParams    Optional Query parameters for the URI.
     * @return Request              The generated PSR-7 request.
     */
    protected function createRequest(
        string $method,
        string $path,
        ?array $post = null,
        ?array $files = null,
        ?array $headers = null,
        ?array $cookies = null,
        ?array $serverParams = null,
        ?array $queryParams = null
    ): Request {
        $psr17Factory = new Psr17Factory();
        $creator = new ServerRequestCreator(
            $psr17Factory, // ServerRequestFactory
            $psr17Factory, // UriFactory
            $psr17Factory, // UploadedFileFactory
            $psr17Factory  // StreamFactory
        );

        // Merge custom server parameters with defaults
        $serverParams = array_merge(
            [
                'HTTP_HOST' => 'localhost',
                'REQUEST_METHOD' => $method,
                'REQUEST_URI' => $path,
            ],
            $serverParams ?? []
        );

        return $creator->fromArrays(
            $serverParams ?? [],
            $headers ?? ['HTTP_ACCEPT' => 'application/json'],
            $cookies ?? [],
            $queryParams ?? [], // Query parameters (empty by default)
            $post, // Default to an empty array if null
            $files ?? []
        );
    }

    /**
     * Creates a request with CSRF token.
     *
     * @param App $app              The Slim application instance.
     * @param string $path          The request path.
     * @param string $method        The request method. i.e POST, DELETE
     * @param array|null $post      Optional POST data.
     * @param array $files          Optional uploaded files.
     * @param array $headers        Optional headers.
     * @param array $cookies        Optional cookies.
     * @param array $serverParams   Optional server parameters.
     * @return Request              The generated PSR-7 request.
     */
    protected function createRequestWithCsrf(
        App $app,
        string $path,
        string $method = 'POST',
        ?array $post = null,
        array $files = [],
        array $headers = ['HTTP_ACCEPT' => 'application/json'],
        array $cookies = [],
        array $serverParams = [],
    ): Request {
        $request = $this->createRequest(
            $method,
            $path,
            $post,
            $files,
            $headers,
            $cookies,
            $serverParams
        );

        $csrfData = $this->getCsrfTokenData($app);
        $request = $request
            ->withHeader($csrfData['csrf_name_key'], $csrfData['csrf_name'])
            ->withHeader($csrfData['csrf_value_key'], $csrfData['csrf_value']);

        return $request;
    }

    /**
     * Creates a POST request with CSRF token injection.
     *
     * If `$withCsrf` is enabled, this method fetches CSRF tokens from the app instance
     * and adds them to the request headers.
     *
     * @param App $app              The Slim application instance.
     * @param string $path          The request path.
     * @param array|null $post      Optional POST data.
     * @param array $files          Optional uploaded files.
     * @param array $headers        Optional headers.
     * @param array $cookies        Optional cookies.
     * @param array $serverParams   Optional server parameters.
     * @param bool $withCsrf        Whether to include CSRF tokens in the request.
     * @return Request              The generated PSR-7 request.
     * @deprecated 0.0.1 Use createRequestWithCsrf instead
     */
    protected function createPostRequestWithCsrf(
        App $app,
        string $path,
        ?array $post = null,
        array $files = [],
        array $headers = ['HTTP_ACCEPT' => 'application/json'],
        array $cookies = [],
        array $serverParams = [],
    ): Request {
        $request = $this->createRequestWithCsrf(
            $app,
            $path,
            'POST',
            $post,
            $files,
            $headers,
            $cookies,
            $serverParams
        );
        return $request;
    }

    /**
     * Retrieves CSRF token data from the application.
     *
     * This method sends a GET request to the configured CSRF token route and extracts
     * the CSRF token values from the response body.
     *
     * @param App $app The Slim application instance.
     * @return array   The CSRF token data containing 'csrf_name_key', 'csrf_name', 'csrf_value_key', and 'csrf_value'.
     */
    protected function getCsrfTokenData(App $app): array
    {
        $csrfRoute = config()->get('csrf')['route'];
        $getRequest = $this->createRequest('GET', $csrfRoute);
        $getResponse = $app->handle($getRequest);

        $body = (string) $getResponse->getBody();
        $data = json_decode($body, true);

        return $data['data'] ?? [];
    }

    /**
     * Creates a PSR-7 GET server request with optional query parameters.
     *
     * This is only a wrapper method for easy use of commone GET request.
     *
     * @param string $path          Request URI.
     * @param array $queryParams    Optional Query parameters for the URI.
     * @return Request              The generated PSR-7 request.
     */
    protected function createGetRequest(
        string $path,
        array $queryParams = []
    ): Request {
        return $this->createRequest(
            'GET',
            $path,
            null,
            null,
            null,
            null,
            null,
            $queryParams
        );
    }

    /**
     * Generate a URL for a named route with optional path and query parameters.
     *
     * This method uses the Slim route parser to construct a URL for the given route name.
     *
     * @param string $routeName     The name of the route as defined in the route definition.
     * @param array  $pathParams    Associative array of path parameters to replace in the route (e.g., ['id' => 123]).
     * @param array  $queryParams   Associative array of query parameters to append to the URL (e.g., ['page' => 2]).
     *
     * @return string               The fully constructed URL for the route.
     */
    protected function generateRouteUrl(string $routeName, array $pathParams = [], array $queryParams = []): string
    {
        $routeParser = $this->getAppInstance()->getRouteCollector()->getRouteParser();
        return $routeParser->urlFor($routeName, $pathParams, $queryParams);
    }
}
