<?php

declare(strict_types=1);

namespace App\Core\Interfaces;
use Psr\Http\Message\ResponseInterface;

interface HttpResponseAdapterInterface extends ResponseInterface
{
    /**
     * Send the actual HTTP response
     *
     * @return static
     */
    public function send(): static;
}
