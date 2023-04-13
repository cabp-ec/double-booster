<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Interfaces\ViewsHandlerInterface;
use App\Core\Traits\ErrorContentNegotiationTrait;

class ViewsHandler implements ViewsHandlerInterface
{
    use ErrorContentNegotiationTrait;

    private ?object $engine = null;

    /**
     * A generic views handler
     *
     * @param string $templatesPath
     * @param string $ext
     */
    public function __construct(string $templatesPath, private readonly string $ext = 'html')
    {
        // TODO: setup your preferred template engine here
        // Example: $this->engine = new TwigEnvironment($loader);
    }

    private function renderRaw(array $values): string
    {
        $title = array_shift($values);
        $valueBody = '<h2>' . $title . '</h2>';
        $valueBody .= '<p>' . implode('</p><p>', $values) . '</p>';

        return '<section>' . $valueBody . '</section>';
    }

    /**
     * @inheritDoc
     */
    public function render(string $view, array $data = [], string $template = 'default'): string
    {
        // Customize the template engine here
        // If there's no engine, we simply use <p> to display data
        if ($this->engine) {
            return $this->engine->render($view . '.' . $this->ext, $data);
        }

        return $this->renderRaw($data);
    }
}
