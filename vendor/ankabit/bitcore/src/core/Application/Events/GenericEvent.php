<?php

declare(strict_types=1);

namespace BitCore\Application\Events;

/**
 * Class GenericEvent
 *
 * A flexible, reusable event class for dispatching events with contextual information,
 * an action name, and an optional payload. Supports stopping propagation and passing
 * a result value.
 *
 * Can be used across repositories, services, modules, controllers, or any custom dispatcher.
 *
 * @package BitCore\Application\Events
 */
class GenericEvent
{
    /**
     * The context (class or service) that dispatched the event.
     *
     * @var string
     */
    protected string $origin;

    /**
     * The action associated with this event.
     *
     * @var string
     */
    protected string $action;

    /**
     * Data attached to this event.
     *
     * @var array
     */
    protected array $payload;

    /**
     * Whether propagation should continue after this event.
     *
     * @var bool
     */
    protected bool $shouldContinue = true;

    /**
     * The class that stopped propagation.
     *
     * @var string|null
     */
    protected ?string $stoppedBy = null;

    /**
     * Optional result to be passed when propagation is stopped.
     *
     * @var mixed|null
     */
    protected mixed $result = null;

    /**
     * GenericEvent constructor.
     *
     * @param string $origin  The origin class or service dispatching the event.
     * @param string $action  The name of the action.
     * @param array  $payload Optional event data.
     */
    public function __construct(string $origin, string $action, array $payload = [])
    {
        $this->origin  = $origin;
        $this->action  = $action;
        $this->payload = $payload;
    }

    /**
     * Get the origin class or service name.
     *
     * @return string
     */
    public function getOrigin(): string
    {
        return $this->origin;
    }

    /**
     * Get the event action name.
     *
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * Get all event payload data.
     *
     * @return array
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    /**
     * Get a specific value from the payload.
     *
     * @param string $key
     * @param mixed  $default
     * @return mixed
     */
    public function getPayloadValue(string $key, mixed $default = null): mixed
    {
        return $this->payload[$key] ?? $default;
    }

    /**
     * Set or override a payload value.
     *
     * @param string $key
     * @param mixed  $value
     * @return void
     */
    public function setPayloadValue(string $key, mixed $value): void
    {
        $this->payload[$key] = $value;
    }

    /**
     * Check whether propagation should continue.
     *
     * @return bool
     */
    public function shouldContinue(): bool
    {
        return $this->shouldContinue;
    }

    /**
     * Stop event propagation and optionally capture the stopping class.
     *
     * @param string|null $listenerClass
     * @return void
     */
    public function stopPropagation(?string $listenerClass = null): void
    {
        $this->shouldContinue = false;
        $this->stoppedBy = $listenerClass;
    }

    /**
     * Set a result to be returned when propagation stops.
     *
     * @param mixed $result
     * @return void
     */
    public function setResult(mixed $result): void
    {
        $this->result = $result;
    }

    /**
     * Get the result value.
     *
     * @return mixed|null
     */
    public function getResult(): mixed
    {
        return $this->result;
    }

    /**
     * Get the class that stopped propagation.
     *
     * @return string|null
     */
    public function getStoppedBy(): ?string
    {
        return $this->stoppedBy;
    }
}
