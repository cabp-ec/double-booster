<?php

namespace App\Interfaces;

use App\Core\ErrorHandler;
use App\Core\Services;
use App\Core\Modules;
use App\Core\Session;

interface ContainerInterface
{
    /**
     * Get the default Error Handler
     *
     * @return ErrorHandler
     */
    public function errorHandler(): ErrorHandler;

    /**
     * Get the default Session Handler
     *
     * @return Session
     */
    public function sessionHandler(): Session;

    /**
     * Get the Services Pool
     *
     * @return Services
     */
    public function services(): Services;

    /**
     * Get the Modules Pool
     *
     * @return Modules
     */
    public function modules(): Modules;
}
