<?php

declare(strict_types=1);

namespace App\Core\Interfaces;

interface LoggableInterface
{
    /**
     * Get the throwable type
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Get a formatted log entry
     *
     * @return string
     */
    public function getLogEntry(): string;
}
