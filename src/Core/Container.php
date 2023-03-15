<?php

declare(strict_types=1);

namespace App\Core;

use App\Interfaces\ContainerInterface;

class Container implements ContainerInterface
{
    public function __construct(
        private ErrorHandler $errorHandler,
        private Session $sessionHandler,
        private Services $servicesPool,
        private Modules $modulesPool
    )
    {
    }

    /**
     * @inheritDoc
     */
    public function errorHandler(): ErrorHandler {
        return $this->errorHandler;
    }

    /**
     * @inheritDoc
     */
    public function sessionHandler(): Session {
        return $this->sessionHandler;
    }

    /**
     * @inheritDoc
     */
    public function services(): Services
    {
        return $this->servicesPool;
    }

    /**
     * @inheritDoc
     */
    public function modules(): Modules
    {
        return $this->modulesPool;
    }
}
