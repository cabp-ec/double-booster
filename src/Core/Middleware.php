<?php

declare(strict_types=1);

namespace App\Core;

use App\Interfaces\PipelineHandlerInterface;
use App\Interfaces\WorkerMiddlewareInterface;
use ReflectionClass;
use ReflectionException;

final class Middleware extends ConfigReceptor implements PipelineHandlerInterface
{
    const MIDDLEWARE_SUFFIX = 'Middleware';
    const MIDDLEWARE_SIGNATURE = 'App\Http\\' . self::MIDDLEWARE_SUFFIX;

    private array $chainDefinitions = [];
    private array $workersFree = [];
    private array $workersOccupied = [];

    /**
     * The Middleware class
     */
    public function __construct(array $config)
    {
        // 1. Clean-up config values
        $this->configCleanUp($config, $this->chainDefinitions);
    }

    /**
     * Clean-up a single pipeline
     *
     * @param string $key
     * @param array $dirty
     * @param array $clean
     * @return array
     */
    private function pipelineCleanUp(string $key, array $dirty, array $clean): array
    {
        $pipeline = [];
        $cleanKeys = array_keys($clean);

        foreach ($dirty[$key] as $pipeKey => $params) {
            if ($pipeKey === $params) { // same key-value, e.g. 'AUTH' => 'AUTH'
                $pipeline = array_merge_recursive(
                    $pipeline,
                    in_array($pipeKey, $cleanKeys) ? $clean[$pipeKey] : $dirty[$pipeKey]
                );
            } else {
                $pipeline[$pipeKey] = $params;
            }

            if (!isset($this->workersFree[$pipeKey])) {
                $this->workersFree[$pipeKey] = [];
            }

            if (!isset($this->workersOccupied[$pipeKey])) {
                $this->workersOccupied[$pipeKey] = [];
            }
        }

        return $pipeline;
    }

    /**
     * Check if there are workers available
     *
     * @param string $key
     * @return bool
     */
    private function hasFreeWorkers(string $key): bool
    {
        return boolval(count($this->workersFree[$key]));
    }

    /**
     * @inheritDoc
     */
    protected function configCleanUp(array $dirty, array &$clean): void
    {
        $clean = [];

        // 1. Look for repeated keys within the array
        // 2. Create the actual tree
        foreach ($dirty as $key => $pipeline) {
            $clean[$key] = $this->pipelineCleanUp($key, $dirty, $clean);
        }
    }

    /**
     * @inheritDoc
     */
    public function getWorker(string $parentKey, string $key): ?WorkerMiddlewareInterface
    {
        /** @var WorkerMiddlewareInterface $worker */
        $worker = null;
        $classSignature = self::MIDDLEWARE_SIGNATURE . '\\' . $key . self::MIDDLEWARE_SUFFIX;

        if (!class_exists($classSignature) && method_exists($classSignature, 'process')) {
            return null;
        }

        if (!$this->hasFreeWorkers($key)) {
            try {
                $worker = (new ReflectionClass($classSignature))->newInstanceArgs([$key]);
            } catch (ReflectionException $e) {
                // TODO: catch this exception with our own error/exception handler
            }
        } else {
            $worker = array_pop($this->workersFree[$key]);
        }

        if (!$worker) {
            return null;
        }

        $worker->setKey($key);
        $worker->setParentKey($parentKey); // TODO: we don't really need this line, re-check and confirm
        $this->workersOccupied[$key][spl_object_hash($worker)] = $worker;

        return $worker;
    }

    /**
     * @inheritDoc
     */
    public function getPipeline(string $key): array
    {
        $pipeline = [];
        $key = strtoupper($key);
        $defKeys = array_keys($this->chainDefinitions);

        if (!in_array($key, $defKeys)) {
            return [];
        }

        foreach ($this->chainDefinitions as $pipelineDef) {
            foreach ($pipelineDef as $workerKey => $params) {
                $worker = $this->getWorker($key, $workerKey);

                if ($worker) {
                    $pipeline[] = $worker;
                } else {
                    // TODO: log the lack of worker using our logger service
                }
            }
        }

        if (empty($pipeline)) {
            // TODO: log an empty pipeline using our logger service
        }

        return $pipeline;
    }

    /**
     * @inheritDoc
     */
    public function dispose(WorkerMiddlewareInterface $worker): void
    {
        $hash = spl_object_hash($worker);
        $workerKey = $worker->getKey();

        if (isset($this->workersOccupied[$workerKey][$hash])) {
            unset($this->workersOccupied[$workerKey][$hash]);
            $this->workersFree[$workerKey][$hash] = $worker;
        }
    }

    /**
     * @inheritDoc
     */
    public function disposePipeline(string $key): void
    {
        $workerKeys = array_keys($this->chainDefinitions[$key]);

        foreach ($workerKeys as $workerKey) {
            foreach ($this->workersOccupied[$workerKey] as $worker) {
                $this->dispose($worker);
            }
        }
    }
}
