<?php

declare(strict_types=1);

namespace BitCore\Modules\Documentation\Actions;

use BitCore\Application\Services\Modules\ModulesManager;
use BitCore\Modules\Documentation\OpenApiProcessors\TagsOrderProcessor;
use BitCore\Modules\Documentation\OpenApiProcessors\RoutePlaceholderProcessor;
use Psr\Http\Message\ResponseInterface as Response;
use OpenApi\Generator as Generator;
use OpenApi\Attributes as OA;
use OpenApi\Annotations as OAAnnotation;
use OpenApi\Processors\DocBlockDescriptions;

#[OA\OpenApi(
    openapi: "3.0.0",
    info: new OA\Info(
        title: "Zarah API Documentation",
        version: "0.1",
        description: "API documentation for Zarah CRM"
    )
)]
class GenerateApiDocumentation extends DocumentationAction
{
    public function action(): Response
    {
        $pattern = '*.php';
        $exclude = ['vendor', 'Tests'];

        $scan_dirs = array_keys(get_module_path_namespace_map());
        hooks()->dispatch('docs_scan_dirs', [&$scan_dirs]);

        $sources = \OpenApi\Util::finder($scan_dirs, $exclude, $pattern);

        // Create the generator instance
        $generator = new Generator(logger());

        // Get the default processors pipeline
        $processorPipeline = $generator->getProcessorPipeline();

        // Insert custom processors early for route tag replacement before other tags
        $processorPipeline->insert(
            new RoutePlaceholderProcessor($this->openApiPlaceholderReplacer->routePlaceholders),
            DocBlockDescriptions::class // Insert before the first processor
        );

        /** @var ModulesManager $moduleManager */
        $moduleManager = container()->get(ModulesManager::class);
        $modules = $moduleManager->getModules();

        $moduleTagPriority = [];
        foreach ($modules as $module) {
            $moduleTagPriority[$module->getId()] = $module->getPriority();
        }

        // Emit event to allow module to customize doc tag display order
        hooks()->dispatch('docs.tag_priority', [&$moduleTagPriority]);
        $processorPipeline->add(
            new TagsOrderProcessor($moduleTagPriority)
        );


        hooks()->dispatch('docs.will_generate', [&$generator]);

        // Generate
        $openapi = $generator->generate(
            $sources,
        );


        // Dynamically set server URL based on request
        $serverUrl = base_url('');
        $server = new OAAnnotation\Server([
            'url' => $serverUrl,
            'description' => 'Default API Server'
        ]);

        $servers = [$server];

        hooks()->dispatch('docs.api_servers', [&$servers]);

        $openapi->servers = $servers;

        // Contextual replacement
        $this->openApiPlaceholderReplacer->replacePlaceholders($openapi);

        // Convert to json
        $json = $openapi->toJson();

        hooks()->dispatch('docs.api_json', [&$json]);

        $this->response->getBody()->write($json);

        return $this->response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}
