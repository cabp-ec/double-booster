<?php

namespace App\Interfaces;

use Psr\Http\Server\MiddlewareInterface;

interface PipelineHandlerInterface
{
    /**
     * Get a worker by key
     *
     * @param string $parentKey
     * @param string $key
     * @return MiddlewareInterface|null
     */
    public function getWorker(string $parentKey, string $key): ?WorkerMiddlewareInterface;

    /**
     * Get a pipeline by key
     *
     * @param string $key
     * @return array
     */
    public function getPipeline(string $key): array;

    /**
     * Dispose a worker
     *
     * @param WorkerMiddlewareInterface $worker
     * @return void
     */
    public function dispose(WorkerMiddlewareInterface $worker): void;

    /**
     * Dispose an entire pipeline
     *
     * @param string $key
     * @return void
     */
    public function disposePipeline(string $key): void;
}
