<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Core\Modules;

interface ModulesHandlerInterface
{
    /**
     * Get a module
     *
     * @param string $key
     * @return ModuleInterface|null
     */
    public function get(string $key): ?ModuleInterface;

    /**
     * Append a module to the pool
     *
     * @param ModuleInterface $module
     * @param string|null $key
     * @return Modules
     */
    public function set(ModuleInterface $module, ?string $key = null): Modules;
}
