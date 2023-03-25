<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Interfaces\ContainerInterface;
use App\Core\Interfaces\ServiceInterface;
use App\Core\Interfaces\ViewsHandlerInterface;
use App\Factories\ServiceFactory;

class Container implements ContainerInterface
{
    /** @var object[] */
    private array $objectsPool = [];

    public function __construct(
        private readonly ErrorHandler          $errorHandler,
        private readonly SessionHandler        $sessionHandler,
        private readonly ServiceFactory        $serviceFactory,
        private readonly ViewsHandlerInterface $viewsHandler
    )
    {
    }

    /**
     * Get the Error Handler
     *
     * @return ErrorHandler
     */
    public function getErrorHandler(): ErrorHandler
    {
        return $this->errorHandler;
    }

    /**
     * Get the Session Handler
     *
     * @return SessionHandler
     */
    public function getSessionHandler(): SessionHandler
    {
        return $this->sessionHandler;
    }

    /**
     * @inheritDoc
     */
    public function getService(string $name): ?ServiceInterface
    {
        return $this->serviceFactory->get($name);
    }

    /**
     * @inheritDoc
     */
    public function getViewsHandler(): ViewsHandlerInterface
    {
        return $this->viewsHandler;
    }

    /**
     * @inheritDoc
     */
    public function get(string $name): ?object
    {
        return $this->getService($name) ?? $objectsPool[$name] ?? null;
    }
}
