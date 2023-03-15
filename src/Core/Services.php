<?php

declare(strict_types=1);

namespace App\Core;

use App\Interfaces\ServicesHandlerInterface;
use App\Interfaces\ServiceInterface;

final class Services extends ConfigReceptor implements ServicesHandlerInterface
{
    private array $config = [];
    private array $pool = [];

    /**
     * The Services class
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
    public function get(string $key): ?ServiceInterface
    {
        if (!in_array($key, array_keys($this->pool))) {
            // TODO: log lack of service using our error/exception handler
        }

        return $this->pool[$key] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function set(ServiceInterface $service, ?string $key = null): Services
    {
        if (!in_array($key, array_keys($this->pool))) {
            $this->pool[$key ?? $service::class] = $service;
        }

        return $this;
    }
}
