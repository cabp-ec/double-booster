<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Core\Services;

interface ServicesHandlerInterface
{
    /**
     * Get a service
     *
     * @param string $key
     * @return ServiceInterface|null
     */
    public function get(string $key): ?ServiceInterface;

    /**
     * Append a service to the pool
     *
     * @param ServiceInterface $service
     * @param string|null $key
     * @return Services
     */
    public function set(ServiceInterface $service, ?string $key = null): Services;
}
