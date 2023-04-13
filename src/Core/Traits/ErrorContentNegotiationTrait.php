<?php

declare(strict_types=1);

namespace App\Core\Traits;

use App\Core\Router;

trait ErrorContentNegotiationTrait
{
    /**
     * Get the given content array as HTML
     *
     * @param array $values
     * @return string
     */
    private function getErrorAsHtml(array $values): string
    {
        $title = array_shift($values);
        $valueTitle = '<h1>Error</h1>';
        $valueCaption = '<h4>' . $title . '</h4>';
        $valueBody = '<p>' . implode('</p><p>', $values) . '</p>';

        return '<section>' . implode('', [$valueTitle, $valueCaption, $valueBody]) . '</section>';
    }

    /**
     * Get the given content array as XML
     *
     * @param array $values
     * @return string
     */
    private function getErrorAsXml(array $values): string
    {
        $title = array_shift($values);
        $valueType = '<title>' . $title . '</title>';
        $valueBody = '<message>' . implode('</message><message>', $values) . '</message>';
        $body = '<messages>' . implode('', [$valueType, $valueBody]) . '</messages>';

        return '<error>' . $body . '</error>';
    }

    /**
     * Get the given content array as JSON
     *
     * @param array $values
     * @return string
     */
    private function getErrorAsJson(array $values): string
    {
        $title = array_shift($values);
        $content = [
            'title' => $title,
            'messages' => $values,
        ];

        return json_encode($content);
    }

    private function getErrorContent(string $contentType, array $values): string
    {
        return match ($contentType) {
            Router::ACCEPTABLE_CONTENT_TYPES[0] => $this->getErrorAsHtml($values),
            Router::ACCEPTABLE_CONTENT_TYPES[1], Router::ACCEPTABLE_CONTENT_TYPES[2] => $this->getErrorAsXml($values),
            default => $this->getErrorAsJson($values),
        };
    }
}
