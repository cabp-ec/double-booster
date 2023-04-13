<?php

declare(strict_types=1);

namespace App\Core\Interfaces;

interface FactoryInterface
{
    /**
     * Get a built object
     *
     * @param string $name The fully qualified class name
     * @return object|null
     */
    public function get(string $name): ?object;
}
