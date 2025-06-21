<?php

declare(strict_types=1);

namespace BitCore\Application\Actions;

use JsonSerializable;
use Throwable;

/**
 * Represents an error that occurred during action execution.
 *
 * This class provides a structured way to represent and handle errors
 * encountered during the execution of application actions or route endpoints.
 */
class ActionError implements JsonSerializable
{
    /**
     * Predefined error types.
     */
    public const BAD_REQUEST = 'BAD_REQUEST';
    public const INSUFFICIENT_PRIVILEGES = 'INSUFFICIENT_PRIVILEGES';
    public const NOT_ALLOWED = 'NOT_ALLOWED';
    public const NOT_IMPLEMENTED = 'NOT_IMPLEMENTED';
    public const RESOURCE_NOT_FOUND = 'RESOURCE_NOT_FOUND';
    public const SERVER_ERROR = 'SERVER_ERROR';
    public const UNAUTHENTICATED = 'UNAUTHENTICATED';
    public const VALIDATION_ERROR = 'VALIDATION_ERROR';
    public const VERIFICATION_ERROR = 'VERIFICATION_ERROR';

    /**
     * @var string The type of error.
     */
    protected string $type;

    /**
     * @var string|null A human-readable description of the error.
     */
    protected ?string $description;

    /**
     * @var mixed The exception details associated with the error, if available.
     */
    protected $exception = null;

    /**
     * Constructs a new ActionError instance.
     *
     * @param string $type The type of error.
     * @param string|null $description An optional description of the error.
     * @param mixed $exception The associated exception, if any.
     */
    public function __construct(string $type, ?string $description = null, $exception = null)
    {
        $this->type = $type;
        $this->description = $description;
        $this->exception = $exception;
    }

    /**
     * Gets the type of error.
     *
     * @return string The type of error.
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Sets the type of error.
     *
     * @param string $type The new type of error.
     * @return self The current ActionError instance for method chaining.
     */
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Gets the description of the error.
     *
     * @return string|null The description of the error, or null if no description is available.
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Sets the description of the error.
     *
     * @param string|null $description The new description of the error.
     * @return self The current ActionError instance for method chaining.
     */
    public function setDescription(?string $description = null): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Gets the associated exception.
     *
     * @return mixed The exception instance, or null if not set.
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * Sets the associated exception.
     *
     * @param mixed $exception The exception to associate with the error.
     * @return self The current ActionError instance for method chaining.
     */
    public function setException($exception = null): self
    {
        $this->exception = $exception;
        return $this;
    }

    /**
     * Specifies how the object should be serialized to JSON.
     *
     * @return array The data to be serialized to JSON.
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return [
            'type' => $this->type,
            'description' => $this->description,
            'exception' => $this->exception,
        ];
    }
}
