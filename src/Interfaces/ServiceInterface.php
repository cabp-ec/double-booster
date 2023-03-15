<?php

namespace App\Interfaces;

interface ServiceInterface
{
    /**
     * Perform a health-check on this service
     *
     * @return bool
     */
    public function healthCheck(): bool;
}
