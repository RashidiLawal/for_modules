<?php

declare(strict_types=1);

namespace BitCore\Application\Actions;

use BitCore\Application\Exception\HttpBadRequestException;
use BitCore\Application\Exception\HttpException;
use BitCore\Application\Services\Requests\RequestInput;
use BitCore\Application\Services\Requests\RequestValidator;
use BitCore\Foundation\Container;
use BitCore\Foundation\Validation\Factory as ValidationFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\StreamInterface;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Base class for all application actions.
 *
 * This class provides common functionality for all action classes,
 * such as dependency injection, request handling, response creation,
 * and error handling.
 */
abstract class Action
{
    /**
     * @var Container The application container.
     */
    protected Container $container;

    /**
     * @var LoggerInterface The application logger.
     */
    protected LoggerInterface $logger;

    /**
     * @var ValidationFactory The validation factory.
     */
    protected ValidationFactory $validator;

    /**
     * @var Request The incoming request.
     */
    protected Request $request;

    /**
     * @var Response The outgoing response.
     */
    protected Response $response;

    /**
     * @var RequestInput The request input data.
     */
    protected RequestInput $input;

    /**
     * @var array The route arguments.
     */
    protected array $args;

    /**
     * Constructs a new Action instance.
     *
     * @param Container $container The application container.
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->logger = $this->container->get(LoggerInterface::class);
        $this->validator = $this->container->get(ValidationFactory::class);
        $this->afterConstruct();
    }

    /**
     * Handles the incoming request and returns a response.
     *
     * @param Request $request The incoming request.
     * @param Response $response The outgoing response.
     * @param array $args The route arguments.
     *
     * @throws HttpException If an error occurs during request handling.
     * @throws HttpException If invalid input is provided.
     *
     * @return Response The response to be sent to the client.
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $this->request = $request;
        $this->response = $response;
        $this->args = $args;

        // Initialize request input
        $this->input = new RequestInput($request, true);

        // Initialize request validator
        RequestValidator::init($this->validator, $this->input);

        try {
            return $this->action();
        } catch (\Throwable $e) {
            throw new HttpException(
                $this->request,
                $e->getMessage(),
                (int)$e->getCode(),
                $e
            );
        }
    }

    /**
     * Performs any necessary actions after the constructor is called.
     *
     * This method can be overridden by child classes to perform
     * initialization tasks.
     */
    protected function afterConstruct(): void
    {
    }

    /**
     * Executes the main logic of the action.
     *
     * @throws Throwable
     *
     * @return Response The response to be sent to the client.
     */
    abstract protected function action(): Response;

    /**
     * Gets the form data from the request body.
     *
     * @return array|object The form data.
     */
    protected function getFormData()
    {
        return $this->request->getParsedBody();
    }

    /**
     * Resolves a route argument.
     *
     * @param string $name The name of the argument.
     * @param bool $required If the parameter should be required or not
     *
     * @throws HttpBadRequestException If the argument is not found.
     *
     * @return mixed The value of the argument.
     */
    protected function resolveArg(string $name, bool $required = true)
    {
        if ($required && !isset($this->args[$name])) {
            throw new HttpBadRequestException($this->request, "Could not resolve argument `{$name}`.");
        }

        return $this->args[$name] ?? null;
    }

    /**
     * Creates a response with the given data and status code.
     *
     * @param array|object|null $data The data to be included in the response.
     * @param int $statusCode The HTTP status code. Defaults to 200 (OK).
     *
     * @return Response The generated response.
     */
    protected function respondWithData($data = null, int $statusCode = 200): Response
    {
        $payload = new ActionPayload($statusCode, $data);

        return $this->respond($payload);
    }

    /**
     * Creates a response from an ActionPayload object.
     *
     * @param ActionPayload $payload The action payload.
     *
     * @return Response The generated response.
     */
    protected function respond(ActionPayload $payload): Response
    {
        $json = json_encode($payload, JSON_PRETTY_PRINT);
        return $this->respondWithJson($json, $payload->getStatusCode());
    }

    /**
     * Creates a response from an json string.
     *
     * @param string $json The json response
     * @param int $statusCode The HTTP status code to use
     *
     * @return Response The generated response.
     */
    protected function respondWithJson($json, int $statusCode = 200): Response
    {
        $this->response->getBody()->write($json);

        return $this->response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($statusCode);
    }

    /**
     * Responds with a file in the HTTP response.
     *
     * This method sets up the appropriate headers and body for serving a file,
     * allowing for both inline display and attachment downloads. It supports
     * custom content length, disposition type, and additional headers.
     *
     * @param string|StreamInterface $content The file content to be sent in the response body
     * @param string $filename The filename to be used in the Content-Disposition header
     * @param string $contentType The MIME type of the file (default: 'application/octet-stream')
     * @param array $options Additional options for the response:
     *                      - 'contentLength' (int|null): The length of the content in bytes
     *                      - 'disposition' (string): Either 'inline' or 'attachment'
     *                      - 'headers' (array): Additional headers to include in the response
     * @return \Psr\Http\Message\ResponseInterface The configured response object
     */
    protected function respondWithFile(
        string|StreamInterface $content,
        string $filename,
        string $contentType = 'application/octet-stream',
        array $options = []
    ) {
        $contentIsString = is_string($content);
        $contentLength = $options['contentLength'] ?? null;
        $disposition = $options['disposition'] ?? 'inline'; // Default to 'inline', can be 'attachment'

        if ($contentLength) {
            $this->response->withHeader('Content-Length', (string) $contentLength);
        }

        // Add any custom headers from options
        $headers = $options['headers'] ?? [];
        if (!empty($headers)) {
            foreach ($headers as $key => $value) {
                $this->response->withHeader($key, $value);
            }
        }

        if ($contentIsString) {
            $this->response->getBody()->write($content);
        } else {
            $this->response->withBody($content);
        }

        return $this->response
            ->withHeader('Content-Type', $contentType)
            ->withHeader('Content-Disposition', "{$disposition}; filename=\"{$filename}\"")
            ->withStatus(200);
    }

    /**
     * Responds with a file download in the HTTP response.
     *
     * This is a convenience method that forces the file to be downloaded
     * (rather than displayed inline) by setting the disposition to 'attachment'.
     * It delegates to respondWithFile() after setting this option.
     *
     * @param mixed $content The file content to be sent in the response body
     * @param string $filename The filename to be used in the Content-Disposition header
     * @param string $contentType The MIME type of the file (default: 'application/octet-stream')
     * @param array $options Additional options for the response (see respondWithFile())
     * @return \Psr\Http\Message\ResponseInterface The configured response object
     */
    protected function respondWithDownload(
        mixed $content,
        string $filename,
        string $contentType = 'application/octet-stream',
        array $options = []
    ) {
        // Merge the 'attachment' disposition option with any other options
        return $this->respondWithFile(
            $content,
            $filename,
            $contentType,
            array_merge(['disposition' => 'attachment'], $options)
        );
    }
}
