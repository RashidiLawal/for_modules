<?php

declare(strict_types=1);

namespace BitCore\Modules\ModulesManager\Events;

use BitCore\Application\Events\GenericEvent;

class ModuleActionEvent extends GenericEvent
{
    /**
     * Builds a module event name for pre- or post-action hooks.
     *
     * @param string $action The module action (e.g., install, activate).
     * @param bool $before Whether to generate a pre-action (before) or post-action (after) event name.
     * @return string The event name (e.g., module.before.install, module.after.activate).
     */
    protected function getModuleEventName(string $action, bool $before = false): string
    {
        $prefix = $before ? 'before' : 'after';
        return "module.{$prefix}.{$action}";
    }
}
