<?php

declare(strict_types=1);

namespace BitCore\Modules\Documentation\Services;

use BitCore\Application\Services\Modules\ModulesManager;
use BitCore\Application\Services\Modules\ModuleInterface;
use OpenApi\Annotations\OpenApi;

/**
 * Service to handle placeholder replacements in OpenAPI annotations.
 *
 * This service replaces placeholders like [MODULE_NAME] and [POSTMAN_URL]
 * within tag and schema descriptions based on the file path context,
 * detecting the module name from the MODULES_PATH-relative path.
 *
 * @package BitCore\Modules\Documentation\Services
 */
class OpenApiPlaceholderReplacer
{
    public $routePlaceholders = [];
    public $placeholders = [];
    public function __construct()
    {
        $routes = app()->getRouteCollector()->getRoutes();
        foreach ($routes as $route) {
            $name = $route->getName() ?? '';
            if (!empty($name)) {
                if (isset($this->routePlaceholders["[ROUTE:$name]"])) {
                    exit($this->routePlaceholders["[ROUTE:$name]"]);
                }
                $this->routePlaceholders["[ROUTE:$name]"] = $route->getPattern();
            }
        }
    }
    /**
     * Replaces placeholders in OpenAPI tags and schema descriptions.
     *
     * @param OpenApi $openapi The OpenAPI instance to process.
     * @return void
     */
    public function replacePlaceholders(OpenApi $openapi): void
    {
        // Process tags and component schemas
        $this->processDescriptions($openapi->tags);
        if (!empty($openapi->components->schemas)) {
            $this->processDescriptions($openapi->components->schemas);
        }
        $this->processPaths($openapi->paths);
    }

    /**
     * Processes and normalizes the OpenAPI paths array by:
     * 1. Replacing route template placeholders in each pathItem's path property with actual values.
     * 2. Merging multiple pathItems with the same normalized path into a single pathItem
     *    by combining HTTP methods (get, post, put, etc.) under that path.
     *
     * @param array $paths Reference to the array of OpenAPI path items to process and normalize.
     *                    Each item should have a 'path' property and potentially HTTP method operations.
     * @return void Modifies $paths in place to be normalized and placeholders replaced.
     */
    protected function processPaths(array &$paths): void
    {
        //foreach ($paths as $key => $pathItem) {
        //  $pathItem->path = $this->interpolatePlaceholders($pathItem->path, $this->routePlaceholders);
        //}

        // Normalize the path after replacing routes unique route name with non unique paths.
        // Normalizing prevent swagger-ui 'duplicated mapping keys'
        $paths = $this->normalizePaths($paths);
    }

    /**
     * Normalizes OpenAPI paths by merging path items with the same path.
     *
     * For example, if multiple pathItems share the same path string but differ in HTTP methods,
     * this method merges their HTTP methods under a single path item.
     *
     * @param array $paths Array of pathItem objects, each containing:
     *                     - path (string): the path string,
     *                     - HTTP method operations as properties (get, post, put, etc.).
     * @return array Normalized array where each path string appears once, with all its methods merged.
     */
    protected function normalizePaths(array $paths): array
    {
        $normalized = [];
        $httpMethods = ['get', 'put', 'post', 'delete', 'options', 'head', 'patch', 'trace'];

        foreach ($paths as $pathItem) {
            // If this path not yet added, add it directly
            if (!isset($normalized[$pathItem->path])) {
                $normalized[$pathItem->path] = $pathItem;
                continue;
            }

            // Merge HTTP methods from current pathItem into the existing normalized pathItem
            foreach ($httpMethods as $method) {
                if (isset($pathItem->{$method})) {
                    $normalized[$pathItem->path]->{$method} = $pathItem->{$method};
                }
            }
        }

        // Return normalized paths as indexed array, discarding keys
        return array_values($normalized);
    }

    /**
     * Iterates over OpenAPI items and replaces placeholders in their descriptions.
     *
     * @param iterable $items The OpenAPI items (tags, schemas).
     * @return void
     */
    protected function processDescriptions(iterable $items): void
    {
        foreach ($items as $item) {
            // Attempt to retrieve the file path from the annotation context
            $filePath = $item->_context->filename ?? null;
            $moduleName = $this->getModuleNameFromContext($filePath);

            if (!$moduleName || !isset($item->description)) {
                continue;
            }

            $this->placeholders['[MODULE_NAME]'] = $moduleName;
            $this->placeholders['[POSTMAN_URL]'] = '/api/docs/postman?name=' . $moduleName;

            // Replace placeholders in the item's description
            $item->description = $this->interpolatePlaceholders($item->description, $this->placeholders);
        }
    }

    /**
     * Retrieves the module name based on the file path relative to MODULES_PATH.
     *
     * @param string|null $filePath The absolute file path.
     * @return string|null The detected module name, or null if not found.
     */
    private function getModuleNameFromContext(?string $filePath): ?string
    {
        if (!$filePath) {
            return null;
        }

        /** @var ModulesManager|null $moduleManager */
        $moduleManager = container()->get(ModulesManager::class);
        if (!$moduleManager) {
            return null;
        }

        /** @var ModuleInterface|null $module */
        $module = $moduleManager->findModuleFromFilePath($filePath);
        return $module ? $module->getId() : null;
    }

    /**
     * Performs placeholder replacement within a given text.
     *
     * @param string $text The text containing placeholders.
     * @param array<string, string> $placeholders An associative array of placeholder-value pairs.
     * @return string The text with placeholders replaced.
     */
    private function interpolatePlaceholders(string $text, array $placeholders): string
    {
        return str_replace(array_keys($placeholders), array_values($placeholders), $text);
    }
}
