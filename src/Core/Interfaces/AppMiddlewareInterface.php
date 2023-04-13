<?php

declare(strict_types=1);

namespace App\Core\Interfaces;

use Psr\Http\Server\MiddlewareInterface;

interface AppMiddlewareInterface extends MiddlewareInterface
{
    /**
     * Get the default error body formatted as per the given content-type
     *
     * @param string $type
     * @param string $contentType
     * @return string
     */
    public function getErrorBody(string $type, string $contentType): string;
}
