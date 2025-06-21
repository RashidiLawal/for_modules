<?php

declare(strict_types=1);

namespace BitCore\Kernel\Handlers;

use BitCore\Application\Actions\ActionError;
use BitCore\Application\Actions\ActionPayload;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpNotImplementedException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Handlers\ErrorHandler as SlimErrorHandler;
use Throwable;

/**
 * Custom error handler that generates JSON responses for exceptions.
 *
 * This class extends Slim's default ErrorHandler to provide custom error responses
 * in a structured format (JSON) with specific error codes and descriptions.
 */
class HttpErrorHandler extends SlimErrorHandler
{
    /**
     * {@inheritdoc}
     *
     * @return Response
     */
    protected function respond(): Response
    {
        $exception = $this->exception;
        $statusCode = 500; // Default status code for internal server errors
        $error = new ActionError(
            ActionError::SERVER_ERROR,
            'An internal error has occurred while processing your request.'
        );

        // Handle specific HTTP exceptions
        if ($exception instanceof HttpException) {
            $statusCode = $exception->getCode();

            if ($statusCode == 0) {
                $statusCode = 500;
            }

            $error->setDescription($exception->getMessage());

            if ($exception instanceof HttpNotFoundException) {
                $error->setType(ActionError::RESOURCE_NOT_FOUND);
            } elseif ($exception instanceof HttpMethodNotAllowedException) {
                $error->setType(ActionError::NOT_ALLOWED);
            } elseif ($exception instanceof HttpUnauthorizedException) {
                $error->setType(ActionError::UNAUTHENTICATED);
            } elseif ($exception instanceof HttpForbiddenException) {
                $error->setType(ActionError::INSUFFICIENT_PRIVILEGES);
            } elseif ($exception instanceof HttpBadRequestException) {
                $error->setType(ActionError::BAD_REQUEST);
            } elseif ($exception instanceof HttpNotImplementedException) {
                $error->setType(ActionError::NOT_IMPLEMENTED);
            }
        }

        // Handle other exceptions if displayErrorDetails is enabled
        if (
            !($exception instanceof HttpException)
            && $exception instanceof Throwable
            && $this->displayErrorDetails
        ) {
            $error->setDescription($exception->getMessage());
        }

        if ($this->displayErrorDetails) {
            // Enforce json exception print here
            $this->contentType = 'application/json';
            $renderer = $this->determineRenderer();
            $exceptionErrorBody = call_user_func($renderer, $this->exception, $this->displayErrorDetails);
            $error->setException($exceptionErrorBody);
        }

        return $this->respondWithPayload($error, $statusCode);
    }

    protected function respondWithPayload(ActionError $error, int $statusCode): Response
    {
        $response = $this->responseFactory->createResponse($statusCode);

        if ($this->exception instanceof HttpMethodNotAllowedException) {
            $allowedMethods = implode(', ', $this->exception->getAllowedMethods());
            $response = $response->withHeader('Allow', $allowedMethods);
        }

        // Create an ActionPayload object
        $payload = new ActionPayload(
            $statusCode,
            ['error' => $error->getDescription()],
            $error
        );

        // Encode the payload as JSON
        $encodedPayload = json_encode($payload, JSON_PRETTY_PRINT);

        // Create the response object
        $response->getBody()->write($encodedPayload);

        // Set the Content-Type header
        return $response->withHeader('Content-Type', 'application/json');
    }
}
