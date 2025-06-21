<?php

declare(strict_types=1);

namespace BitCore\Modules\Documentation\Actions;

use Psr\Http\Message\ResponseInterface as Response;

class GenerateApiDocumentationUi extends DocumentationAction
{
    public function action(): Response
    {
        //@todo Make title from translation
        $title = 'Zarah API Documentation';

        // Load template file content
        $templatePath = __DIR__ . '/../View/swagger.html';
        if (!file_exists($templatePath)) {
            //@todo Make error from translation
            throw new \RuntimeException("Template not found: " . $templatePath);
        }

        $html = file_get_contents($templatePath);

        // Replace placeholders
        $html = str_replace(
            ['{{ title }}'],
            [$title],
            $html
        );

        // Write response
        $this->response->getBody()->write($html);
        return $this->response->withHeader('Content-Type', 'text/html')->withStatus(200);
    }
}
