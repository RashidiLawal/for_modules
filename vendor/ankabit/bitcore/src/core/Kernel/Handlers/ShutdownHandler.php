<?php

declare(strict_types=1);

namespace BitCore\Kernel\Handlers;

use BitCore\Kernel\ResponseEmitter\ResponseEmitter;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpInternalServerErrorException;

/**
 * Handles fatal errors that occur during request processing.
 *
 * This class captures and handles fatal errors (e.g., E_ERROR, E_USER_ERROR)
 * and generates a suitable HTTP response.
 */
class ShutdownHandler
{
    /**
     * @var Request The incoming request.
     */
    private Request $request;

    /**
     * @var HttpErrorHandler The error handler used to generate the error response.
     */
    private HttpErrorHandler $errorHandler;

    /**
     * @var bool Whether or not to display detailed error information in the response.
     */
    protected bool $displayErrorDetails;

    protected bool $logErrors = false;

    protected bool $logErrorDetails = false;

    /**
     * Constructor.
     *
     * @param Request $request The incoming request.
     * @param HttpErrorHandler $errorHandler The error handler.
     * @param bool $displayErrorDetails Whether to display detailed error information.
     * @param bool $logErrors           Whether or not to log errors
     * @param bool $logErrorDetails     Whether or not to log error details
     */
    public function __construct(
        Request $request,
        HttpErrorHandler $errorHandler,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails
    ) {
        $this->request = $request;
        $this->errorHandler = $errorHandler;
        $this->displayErrorDetails = $displayErrorDetails;
        $this->logErrors = $logErrors;
        $this->logErrorDetails = $logErrorDetails;
    }

    /**
     * Handles the fatal error.
     *
     * This method is invoked by register_shutdown_function().
     */
    public function __invoke()
    {
        $error = error_get_last();
        if ($error) {
            $errorFile = $error['file'];
            $errorLine = $error['line'];
            $errorMessage = $error['message'];
            $errorType = $error['type'];

            // Prepare error message based on error type and displayErrorDetails setting
            $message = 'An error while processing your request. Please try again later.';
            if ($this->displayErrorDetails) {
                switch ($errorType) {
                    case E_USER_ERROR:
                        $message = "FATAL ERROR: {$errorMessage}. ";
                        $message .= " on line {$errorLine} in file {$errorFile}.";
                        break;

                    case E_USER_WARNING:
                        $message = "WARNING: {$errorMessage}";
                        break;

                    case E_USER_NOTICE:
                        $message = "NOTICE: {$errorMessage}";
                        break;

                    default:
                        $message = "ERROR: {$errorMessage}";
                        $message .= " on line {$errorLine} in file {$errorFile}.";
                        break;
                }
            }

            // Create an HttpInternalServerErrorException
            $exception = new HttpInternalServerErrorException($this->request, $message);

            // Generate an error response using the HttpErrorHandler
            $response = $this->errorHandler->__invoke(
                $this->request,
                $exception,
                $this->displayErrorDetails,
                $this->logErrors,
                $this->logErrorDetails,
            );

            // Emit the error response
            $responseEmitter = new ResponseEmitter();
            $responseEmitter->emit($response);
        }
    }
}
