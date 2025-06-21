<?php

namespace BitCore\Application\Events;

/**
 * Filter event for csrf excluded paths.
 *
 * This class holds the paths that are excluded from CSRF protection.
 * It is typically used to define which routes do not require CSRF token validation.
 */
final class CsrfExcludedPaths
{
    /** @var array Array of paths that should be excluded from CSRF protection */
    public $paths = [];

    /**
     * Constructor to initialize the list of excluded paths.
     *
     * @param array $paths An array of paths that should be excluded from CSRF protection.
     */
    public function __construct(array $paths)
    {
        $this->paths = $paths;
    }
}
