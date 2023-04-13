<?php

declare(strict_types=1);

namespace App\Core\Interfaces;

use App\Core\ErrorHandler;
use App\Core\SessionHandler;

interface ContainerInterface
{
    /**
     * Get the default Error Handler
     *
     * @return ErrorHandler
     */
    public function getErrorHandler(): ErrorHandler;

    /**
     * Get the default Session Handler
     *
     * @return SessionHandler
     */
    public function getSessionHandler(): SessionHandler;

    /**
     * Get a Service
     *
     * @param string $name
     * @return ServiceInterface|null
     */
    public function getService(string $name): ?ServiceInterface;

    /**
     * Get the views handler
     *
     * @return ViewsHandlerInterface
     */
    public function getViewsHandler(): ViewsHandlerInterface;

    /**
     * Get an object from the container
     *
     * @param string $name
     * @return object|null
     */
    public function get(string $name): ?object;
}
