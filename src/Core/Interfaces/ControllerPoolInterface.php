<?php

declare(strict_types=1);

namespace App\Core\Interfaces;

use App\Core\EncounterPayload;
use Psr\Http\Message\ResponseInterface;

interface ControllerPoolInterface
{
    /**
     * Get a worker
     *
     * @param string $uri
     * @param array $definition
     * @return HttpControllerInterface
     */
    public function get(string $uri, array $definition): HttpControllerInterface;

    /**
     * Dispose a worker
     *
     * @param HttpControllerInterface $worker
     * @return void
     */
    public function dispose(HttpControllerInterface $worker): void;

    /**
     * Handle a single route
     *
     * @param EncounterPayload $payload
     * @return ResponseInterface
     */
    public function operate(EncounterPayload $payload): ResponseInterface;
}
