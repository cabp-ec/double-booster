<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Core\EncounterPayload;
use App\Core\HttpResponse;

interface RouteHandlerInterface
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
     * @return HttpResponse
     */
    public function operate(EncounterPayload $payload): HttpResponse;
}
