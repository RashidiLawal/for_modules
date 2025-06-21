<?php

declare(strict_types=1);

namespace BitCore\Tests\Application\Middleware;

use BitCore\Kernel\App;
use BitCore\Application\Events\CsrfExcludedPaths;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use BitCore\Tests\TestCase;

/**
 * Class CsrfMiddlewareTest
 *
 * Provides integration tests for the Slim CSRF middleware.
 */
class CsrfMiddlewareTest extends TestCase
{
    /**
     * Sets up a test POST route at /test-csrf that returns "Success" with status 200.
     *
     * @param mixed $app The Slim application instance.
     */
    private function setupPostRoute($app): void
    {
        $app->post('/test-csrf', function (ServerRequestInterface $request, ResponseInterface $response) {
            $response->getBody()->write("Success");
            return $response->withStatus(200);
        });
    }

    /**
     * Retrieves CSRF token data by sending a GET request to the configured CSRF route.
     *
     * @param mixed $app The Slim application instance.
     * @return array The CSRF token data.
     */
    protected function getCsrfTokenData(App $app): array
    {
        $data = parent::getCsrfTokenData($app);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('csrf_name_key', $data);
        $this->assertArrayHasKey('csrf_value_key', $data);
        $this->assertArrayHasKey('csrf_name', $data);
        $this->assertArrayHasKey('csrf_value', $data);

        return $data;
    }

    /**
     * Tests that a POST request without CSRF tokens is rejected.
     *
     * The CSRF middleware is expected to return a 400 status code when the
     * required CSRF tokens are missing.
     */
    public function testCsrfMiddlewareRejectsRequestWithoutTokens(): void
    {
        $app = $this->getAppInstance();
        $this->setupPostRoute($app);

        // Create a POST request without including any CSRF tokens.
        $request = $this->createRequest('POST', '/test-csrf', ['test' => 'test']);
        $response = $app->handle($request);

        $this->assertEquals(400, $response->getStatusCode());
    }

    /**
     * Tests that a POST request with valid CSRF tokens in the request body is accepted.
     *
     * The test first retrieves valid token keys/values, then sends them in the parsed body.
     */
    public function testCsrfMiddlewareAcceptsRequestWithValidTokens(): void
    {
        $app = $this->getAppInstance();
        $this->setupPostRoute($app);

        $data = $this->getCsrfTokenData($app);

        $postRequest = $this->createRequest('POST', '/test-csrf');
        $parsedBody = [
            $data['csrf_name_key']  => $data['csrf_name'],
            $data['csrf_value_key'] => $data['csrf_value'],
        ];
        $postRequest = $postRequest->withParsedBody($parsedBody);

        $postResponse = $app->handle($postRequest);

        $this->assertEquals(200, $postResponse->getStatusCode());
        $this->assertEquals("Success", (string)$postResponse->getBody());
    }

    /**
     * Tests that a POST request with invalid CSRF tokens in the request body is rejected.
     *
     * The middleware should return a 400 status code when the tokens in the body are incorrect.
     */
    public function testCsrfMiddlewareRejectsRequestWithInvalidTokensInBody(): void
    {
        $app = $this->getAppInstance();
        $this->setupPostRoute($app);

        $postRequest = $this->createRequest('POST', '/test-csrf');
        $invalidParsedBody = [
            // Arbitrary invalid token values.
            'csrf_name_key'  => 'invalid_name',
            'csrf_value_key' => 'invalid_value',
        ];
        $postRequest = $postRequest->withParsedBody($invalidParsedBody);

        $postResponse = $app->handle($postRequest);
        $this->assertEquals(400, $postResponse->getStatusCode());
    }

    /**
     * Tests that a POST request with valid CSRF tokens provided in headers is accepted.
     *
     * The test sends valid token keys/values as headers instead of in the body.
     */
    public function testCsrfMiddlewareAcceptsRequestWithValidTokensInHeader(): void
    {
        $app = $this->getAppInstance();
        $this->setupPostRoute($app);

        $data = $this->getCsrfTokenData($app);

        $postRequest = $this->createRequest('POST', '/test-csrf')
            ->withHeader($data['csrf_name_key'], $data['csrf_name'])
            ->withHeader($data['csrf_value_key'], $data['csrf_value']);

        $postResponse = $app->handle($postRequest);
        $this->assertEquals(200, $postResponse->getStatusCode());
        $this->assertEquals("Success", (string)$postResponse->getBody());
    }

    /**
     * Tests that a POST request with invalid CSRF tokens provided in headers is rejected.
     *
     * When invalid tokens are provided via headers, the middleware should return a 400 status code.
     */
    public function testCsrfMiddlewareRejectsRequestWithInvalidTokensInHeader(): void
    {
        $app = $this->getAppInstance();
        $this->setupPostRoute($app);

        $data = $this->getCsrfTokenData($app);

        $postRequest = $this->createRequest('POST', '/test-csrf')
            ->withHeader($data['csrf_name_key'], 'invalid_name')
            ->withHeader($data['csrf_value_key'], 'invalid_value');

        $postResponse = $app->handle($postRequest);
        $this->assertEquals(400, $postResponse->getStatusCode());
    }

    /**
     * Tests that a POST request with valid CSRF tokens provided only via cookies is rejected.
     *
     * Even if the cookies contain the valid tokens, the double submission pattern requires that the tokens
     * also be sent in the request body or headers.
     */
    public function testCsrfMiddlewareRejectsRequestWithValidCookiesOnly(): void
    {
        $app = $this->getAppInstance();
        $this->setupPostRoute($app);

        $data = $this->getCsrfTokenData($app);

        $postRequest = $this->createRequest('POST', '/test-csrf');
        $cookies = [
            $data['csrf_name_key']  => $data['csrf_name'],
            $data['csrf_value_key'] => $data['csrf_value']
        ];
        $postRequest = $postRequest->withCookieParams($cookies);

        $postResponse = $app->handle($postRequest);
        $this->assertEquals(400, $postResponse->getStatusCode());
    }

    /**
     * Tests that a POST request with valid CSRF tokens provided in both cookies and the request body is accepted.
     *
     * This simulates a proper double submission by sending the tokens both as cookies and in the parsed body.
     */
    public function testCsrfMiddlewareAcceptRequestWithValidCookiesAndBody(): void
    {
        $app = $this->getAppInstance();
        $this->setupPostRoute($app);

        $data = $this->getCsrfTokenData($app);

        $postRequest = $this->createRequest('POST', '/test-csrf');
        // Set valid tokens in both cookies and request body.
        $cookies = [
            $data['csrf_name_key']  => $data['csrf_name'],
            $data['csrf_value_key'] => $data['csrf_value']
        ];
        $postRequest = $postRequest->withCookieParams($cookies);
        $parsedBody = [
            $data['csrf_name_key']  => $data['csrf_name'],
            $data['csrf_value_key'] => $data['csrf_value']
        ];
        $postRequest = $postRequest->withParsedBody($parsedBody);

        $postResponse = $app->handle($postRequest);
        $this->assertEquals(200, $postResponse->getStatusCode());
        $this->assertEquals("Success", (string)$postResponse->getBody());
    }

    /**
     * Tests that a POST request with valid CSRF tokens provided in both cookies and headers is accepted.
     *
     * This simulates a proper double submission by sending the tokens both as cookies and in headers.
     */
    public function testCsrfMiddlewareAcceptRequestWithValidCookiesAndHeader(): void
    {
        $app = $this->getAppInstance();
        $this->setupPostRoute($app);

        $data = $this->getCsrfTokenData($app);

        $postRequest = $this->createRequest('POST', '/test-csrf');
        // Set valid tokens in both cookies and headers.
        $cookies = [
            $data['csrf_name_key']  => $data['csrf_name'],
            $data['csrf_value_key'] => $data['csrf_value']
        ];
        $postRequest = $postRequest->withCookieParams($cookies)
            ->withHeader($data['csrf_name_key'], $data['csrf_name'])
            ->withHeader($data['csrf_value_key'], $data['csrf_value']);

        $postResponse = $app->handle($postRequest);
        $this->assertEquals(200, $postResponse->getStatusCode());
        $this->assertEquals("Success", (string)$postResponse->getBody());
    }

    /**
     * Ensures that a POST request to an excluded CSRF path is accepted without requiring CSRF tokens.
     *
     * The CSRF middleware should not enforce token validation on explicitly excluded paths.
     * This test registers a new excluded path via the `CsrfExcludedPaths` event and verifies
     * that a POST request to that path succeeds without providing CSRF tokens.
     *
     * Expected Behavior:
     * - The request should not be blocked by CSRF validation.
     * - The response should return a 200 status code, indicating success without requiring tokens.
     */
    public function testCsrfMiddlewareAcceptRequestWithoutTokensForExcludedPaths(): void
    {
        $excludedPath = '/test-csrf';

        $app = $this->getAppInstance();

        // Register the path in the list of CSRF-excluded routes.
        hooks()->listen(CsrfExcludedPaths::class, function (CsrfExcludedPaths $event) use ($excludedPath) {
            $event->paths[] = $excludedPath;
        });

        $this->setupPostRoute($app);

        // Create and send a POST request to the excluded path without CSRF tokens.
        $request = $this->createRequest('POST', $excludedPath, ['test' => 'test']);
        $response = $app->handle($request);

        // Verify that the request is allowed and returns the expected response code.
        $this->assertEquals(200, $response->getStatusCode());
    }
}
