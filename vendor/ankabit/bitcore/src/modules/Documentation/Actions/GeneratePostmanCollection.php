<?php

declare(strict_types=1);

namespace BitCore\Modules\Documentation\Actions;

use Psr\Http\Message\ResponseInterface as Response;
use OpenApi\Generator as Generator;
use Symfony\Component\Finder\Finder;
use BitCore\Application\Services\Modules\ModulesManager;
use BitCore\Application\Services\Modules\ModuleInterface;

class GeneratePostmanCollection extends DocumentationAction
{
    public function action(): Response
    {
        $queryParams = $this->request->getQueryParams();
        $moduleName = basename($queryParams['name'] ?? '');

        if (empty($moduleName) || !preg_match('/^[a-zA-Z]+$/', $moduleName)) {
            throw new \InvalidArgumentException(trans('Documentation::messages.invalid_module'));
        }

        /** @var ModulesManager $moduleManager */
        $moduleManager = container()->get(ModulesManager::class);

        /** @var ModuleInterface|null $module */
        $module = $moduleManager->findModuleById($moduleName);
        if (!$module) {
            throw new \RuntimeException(trans('Documentation::messages.invalid_module'));
        }

        $modulePath = $module->getBasePath();
        $documentationModulePath = $moduleManager->findModuleById('Documentation')->getBasePath();

        $exclude = ['vendor', 'Tests'];
        $pattern = '*.php';
        // Scan both module and documentation folders
        $sources = $this->getPhpFilesFromFinder(\OpenApi\Util::finder($modulePath, $exclude, $pattern));
        $sources = array_merge(
            $sources,
            $this->getPhpFilesFromFinder(\OpenApi\Util::finder($documentationModulePath, $exclude, $pattern))
        );

        $openapi = Generator::scan($sources);

        $this->openApiPlaceholderReplacer->replacePlaceholders($openapi);

        $postmanCollection = $this->convertToPostmanCollection($openapi->toJson());

        $filename = "postman_collection_{$moduleName}.json";

        return $this->respondWithDownload(json_encode($postmanCollection), $filename, 'application/json');
    }

    private function getPhpFilesFromFinder(Finder $finder): array
    {
        $files = [];
        foreach ($finder as $file) {
            $files[] = $file->getRealPath();
        }
        return $files;
    }


    private function convertToPostmanCollection($openapi): array
    {
        return [
            'info' => [
                'name' => 'Zarah API - ' . ucfirst($this->request->getQueryParams()['name'] ?? 'Unknown'),
                'schema' => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json',
            ],
            'item' => [$openapi], // Here, you should map OpenAPI paths and methods into Postman collection format
        ];
    }
}
