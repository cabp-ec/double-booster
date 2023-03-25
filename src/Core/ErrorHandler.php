<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Errors\AppException;
use App\Core\Interfaces\LoggableInterface;

final class ErrorHandler
{
    /**
     * Constructor for the ErrorHandler class
     *
     * @param FileLogger $logger
     */
    public function __construct(private FileLogger $logger)
    {
    }

    /**
     * Clone this object
     *
     * @return void
     */
    protected function __clone()
    {
    }

    /**
     * @throws AppException
     */
    public function __wakeup()
    {
        throw new AppException('Can\'t wake up');
    }

    /**
     * Catch an error or exception
     *
     * @param LoggableInterface $e
     * @return void
     */
    public function catch(LoggableInterface $e): void
    {
        $this->logger->log($e->getLogEntry(), $e->getType());
    }
}
