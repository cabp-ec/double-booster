<?php

declare(strict_types=1);

namespace App\Core\Interfaces;

interface LoggerInterface
{
    /**
     * Log a message
     *
     * @param string $message
     * @param string $key
     * @return bool
     */
    public function log(string $message, string $key): bool;
}
