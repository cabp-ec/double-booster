<?php

declare(strict_types=1);

namespace App\Core;

use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;
use App\Core\Interfaces\ViewsHandlerInterface;
use App\Core\Traits\ErrorContentNegotiationTrait;

class ViewsHandler implements ViewsHandlerInterface
{
    use ErrorContentNegotiationTrait;

    private ?TwigEnvironment $engine = null;

    /**
     * A generic views handler
     *
     * @param string $templatesPath
     * @param string $ext
     */
    public function __construct(string $templatesPath, private readonly string $ext = 'twig')
    {
        $loader = new FilesystemLoader($templatesPath);
        $this->engine = new TwigEnvironment($loader);
    }

    /**
     * @inheritDoc
     *
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function render(string $view, array $data = [], string $template = 'default'): string
    {
        // Customize the template engine here
        // If there's no engine, we simply use <p> to display data
        if ($this->engine) {
            return $this->engine->render($view . '.' . $this->ext, $data);
        }

        return $this->getErrorAsHtml(array_values(Router::ERROR_DETAIL_404));
    }
}
