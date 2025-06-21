<?php
declare(strict_types=1);

namespace Modules\Todo;
use BitCore\Application\Services\Modules\AbstractModule;
/**
 * Class Todo
 *
 * This class represents a Todo module in the application, responsible for the todo states.
 */
class Todo extends AbstractModule
{
    protected $id = 'Todo';
    protected $name = 'Todo Module';
    protected $description = 'Module to manage todo';
    protected $version = '1.0.0'; // Required attribute
    protected $authorName = 'Lawal Rashidi';
    protected $authorUrl = 'mailto:devrashlaw@gmail.com';
    protected $priority = 20; // Load priority. Higher value to load earlier than other modules
    protected $autoloadRegister = true;  // Autoload register function

    // protected $autoloadRoute = true; // Autoload route function
    
}
