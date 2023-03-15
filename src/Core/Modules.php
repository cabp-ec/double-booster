<?php

declare(strict_types=1);

namespace App\Core;

use App\Interfaces\ModuleInterface;
use App\Interfaces\ModulesHandlerInterface;

final class Modules extends ConfigReceptor implements ModulesHandlerInterface
{
    private array $config = [];
    private array $pool = [];

    /**
     * The Module Pool class
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->configCleanUp($config, $this->config);
    }

    /**
     * @inheritDoc
     */
    protected function configCleanUp(array $dirty, array &$clean): void
    {
        // TODO: Implement configCleanUp() method.
        $clean = $dirty;
    }

    /**
     * @inheritDoc
     */
    public function get(string $key): ?ModuleInterface
    {
        if (!in_array($key, array_keys($this->pool))) {
            // TODO: log lack of module using our error/exception handler
        }

        return $this->pool[$key] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function set(ModuleInterface $module, ?string $key = null): Modules
    {
        if (!in_array($key, array_keys($this->pool))) {
            $this->pool[$key ?? $module::class] = $module;
        }

        return $this;
    }
}
