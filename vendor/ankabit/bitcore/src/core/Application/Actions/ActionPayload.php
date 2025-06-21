<?php

declare(strict_types=1);

namespace BitCore\Application\Actions;

use JsonSerializable;

/**
 * Represents a structured payload for action responses.
 *
 * This class encapsulates the HTTP status code, data, and any error
 * encountered during the action execution.
 */
class ActionPayload implements JsonSerializable
{
    /**
     * @var int The HTTP status code.
     */
    private int $statusCode;

    /**
     * @var array|object|null The data returned by the action. Can be an array, object, or null.
     */
    private $data;

    /**
     * @var ActionError|null The error encountered during action execution.
     */
    private ?ActionError $error;

    /**
     * Constructs a new ActionPayload instance.
     *
     * @param int $statusCode The HTTP status code. Defaults to 200 (OK).
     * @param mixed|null $data The data to be included in the payload.
     * @param ActionError|null $error The error encountered during action execution.
     */
    public function __construct(
        int $statusCode = 200,
        $data = null,
        ?ActionError $error = null
    ) {
        $this->statusCode = $statusCode;
        $this->data = $data;
        $this->error = $error;
    }

    /**
     * Gets the HTTP status code.
     *
     * @return int The HTTP status code.
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Gets the data returned by the action.
     *
     * @return array|null|object The data, which can be an array, object, or null.
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Gets the error encountered during action execution.
     *
     * @return ActionError|null The error object, or null if no error occurred.
     */
    public function getError(): ?ActionError
    {
        return $this->error;
    }

    /**
     * Specifies how the object should be serialized to JSON.
     *
     * @return array The data to be serialized to JSON.
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        $payload = [
            'statusCode' => $this->statusCode,
        ];

        if ($this->data !== null) {
            $payload['data'] = $this->data;
        }

        if ($this->error !== null) {
            $payload['error'] = $this->error;
        }

        return $payload;
    }
}
