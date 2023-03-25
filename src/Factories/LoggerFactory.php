<?php

declare(strict_types=1);

namespace App\Factories;

use App\Core\FileLogger;
use App\Core\Interfaces\LoggerFactoryInterface;

class LoggerFactory implements LoggerFactoryInterface
{
    public function __construct(private readonly string $logPath)
    {
    }

    /**
     * @inheritDoc
     */
    public function createFileLogger(): FileLogger
    {
        return new FileLogger($this->logPath);
    }
}
