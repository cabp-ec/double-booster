<?php

declare(strict_types=1);

namespace App\Core\Interfaces;

interface ServiceInterface
{
    /**
     * Perform a health-check on this service
     *
     * @return bool
     */
    public function healthCheck(): bool;
}
