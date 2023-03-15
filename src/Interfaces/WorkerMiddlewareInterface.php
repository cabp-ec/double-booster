<?php

namespace App\Interfaces;

use Psr\Http\Server\MiddlewareInterface;

interface WorkerMiddlewareInterface extends MiddlewareInterface
{
    /**
     * Get this worker's parent key
     *
     * @return string
     */
    function getParentKey(): string;

    /**
     * Get this worker's key
     *
     * @return string
     */
    function getKey(): string;

    /**
     * Set this worker's parent key
     *
     * @param string $value
     * @return void
     */
    function setParentKey(string $value): void;

    /**
     * Set this worker's key
     *
     * @param string $value
     * @return void
     */
    function setKey(string $value): void;
}
