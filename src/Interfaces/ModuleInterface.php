<?php

namespace App\Interfaces;

interface ModuleInterface
{
    /**
     * Perform a health-check on this module
     *
     * @return bool
     */
    public function healthCheck(): bool;
}
