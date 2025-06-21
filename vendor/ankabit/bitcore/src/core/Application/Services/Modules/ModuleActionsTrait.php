<?php

namespace BitCore\Application\Services\Modules;

use BitCore\Application\Services\Modules\ModuleInterface;
use BitCore\Application\Services\Modules\ModuleRegistry;
use BitCore\Modules\ModulesManager\Events\ModuleActionEvent;
use BitCore\Modules\ModulesManager\Exceptions\ModuleNotFoundException;
use BitCore\Modules\ModulesManager\Exceptions\InvalidModuleActionException;

trait ModuleActionsTrait
{
    /**
     * The module registry instance.
     *
     * @var ModuleRegistry
     */
    protected $moduleRegistry;

    /**
     * The hook dispatcher instance.
     *
     * @var HookDispatcherInterface|null
     */
    protected $hookDispatcher;

    /**
     * Cache of public methods from ModuleInterface.
     *
     * @var array<string>|null
     */
    private $moduleInterfaceMethods;

    /**
     * Central handler for module operations.
     *
     * Performs the specified action on a module, dispatching pre- and post-action events.
     * The action must be a public method defined in ModuleInterface.
     *
     * @param string $moduleId The unique identifier of the module.
     * @param string $action The action to perform (must be a public method of ModuleInterface).
     * @param callable|null $extraCallback The custom callalbe action to perform before the module call.
     * @return ModuleInterface The module instance after the action is performed.
     * @throws ModuleNotFoundException If the module is not found in the registry.
     * @throws InvalidModuleActionException If the action is not a public method of ModuleInterface or not supported by the module.
     */
    protected function handleModuleAction(string $moduleId, string $action, callable $extraCallback = null): ModuleInterface
    {
        // Find module
        $module = $this->getModuleRegistry()->findModuleById($moduleId);
        if (!$module) {
            throw new ModuleNotFoundException(
                trans('modules.module_not_found', ['id' => $moduleId]),
                404
            );
        }

        $moduleActionCallable = is_callable([$module, $action]);
        $extraCallbackValid = is_callable($extraCallback);

        // Validate module supports the action
        if (!$moduleActionCallable && !$extraCallbackValid) {
            throw new InvalidModuleActionException(
                trans(
                    'modules.module_does_not_support_action',
                    [
                        'action' => $action, 'module' => $moduleId
                    ]
                ),
                400
            );
        }

        // Dispatch pre-action event
        $eventName = $this->getModuleEventName($action, true);
        $event = $this->dispatchHook($eventName, $module);
        if (!$event->shouldContinue()) {
            return $module;
        }

        // Perform action
        if ($moduleActionCallable) {
            call_user_func([$module, $action]);
        }

        if ($extraCallbackValid) {
            call_user_func($extraCallback);
        }


        // Dispatch post-action event
        $eventName = $this->getModuleEventName($action, false);
        $this->dispatchHook($eventName, $module);

        return $module;
    }



    /**
     * Dispatches a hook event if a hook dispatcher is available.
     *
     * @param string $eventName The name of the event to dispatch.
     * @param ModuleInterface $module The module instance associated with the event.
     * @return ModuleActionEvent
     */
    protected function dispatchHook(string $eventName, ModuleInterface $module): ModuleActionEvent
    {
        $event = new ModuleActionEvent(
            static::class,
            $eventName,
            [
                'module' => $module
            ]
        );
        hooks()->dispatch($eventName, $event);

        return $event;
    }

    /**
     * Gets the module registry instance.
     *
     * @return ModuleRegistry The module registry instance.
     */
    protected function getModuleRegistry(): ModuleRegistry
    {
        if (!$this->moduleRegistry) {
            $this->moduleRegistry = container(ModuleRegistry::class);
        }
        return $this->moduleRegistry;
    }

    /**
     * Sets the module registry instance.
     *
     * @param ModuleRegistry $moduleRegistry The module registry to use.
     * @return void
     */
    public function setModuleRegistry(ModuleRegistry $moduleRegistry): void
    {
        $this->moduleRegistry = $moduleRegistry;
    }
}
