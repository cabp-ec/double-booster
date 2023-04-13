<?php

declare(strict_types=1);

namespace App\Core\Interfaces;

use App\Core\FileLogger;

interface LoggerFactoryInterface
{
    /**
     * Create a File Logger
     *
     * @return FileLogger
     */
    public function createFileLogger(): FileLogger;
}
