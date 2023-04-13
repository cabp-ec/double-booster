<?php

declare(strict_types=1);

namespace App\Core\Interfaces;

interface ViewsHandlerInterface
{
    /**
     * Get a rendered view
     *
     * @param string $view
     * @param array $data
     * @param string $template
     * @return string
     */
    public function render(string $view, array $data = [], string $template = 'default'): string;
}
