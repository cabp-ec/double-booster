<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Interfaces\HttpResponseAdapterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Relay\Relay;
use App\Core\Errors\AppException;
use App\Core\Interfaces\HttpControllerInterface;
use App\Http\Middleware\ControllerMiddleware;

final class MiddlewareHandler
{
    const MIDDLEWARE_NAMESPACE = 'App\Http\Middleware\\';

    /**
     * Constructor for the MiddlewareHandler class
     */
    public function __construct(private readonly array $pipelinesAvail)
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
     * Create a pipeline node
     *
     * @param string $nodeKey
     * @return array
     */
    private function makeNode(string $nodeKey): array
    {
        $output = [];
        $node = $this->pipelinesAvail[$nodeKey] ?? [];

        foreach ($node as $key => $value) {
            if ($key === $value) {
                $output = array_merge_recursive($output, $this->makeNode($key));
            } else {
                $output[$key] = $value;
            }
        }

        return $output;
    }

    /**
     * Create a pipeline of middlewares
     *
     * @param string $pipelineName
     * @return array
     */
    private function makePipeline(string $pipelineName): array
    {
        $pipeline = [];
        $nodes = $this->makeNode($pipelineName);

        foreach ($nodes as $key => $args) {
            try {
                $className = self::MIDDLEWARE_NAMESPACE . $key . 'Middleware';
                $pipeline[] = (new \ReflectionClass($className))->newInstanceArgs($args);
            } catch (\ReflectionException $e) {
                throw new AppException($e->getMessage(), $e->getCode());
            }
        }

        return $pipeline;
    }

    /**
     * Run the middleware pipeline
     *
     * @param string $pipelineName
     * @param ServerRequestInterface $request
     * @return HttpResponseAdapterInterface
     */
    public function run(string $pipelineName, ServerRequestInterface $request): HttpResponseAdapterInterface
    {
        $pipeline = $this->makePipeline($pipelineName);
        $pipeline[] = new ControllerMiddleware();
        $relay = new Relay($pipeline);

        return new HttpResponseAdapter($request, $relay->handle($request));
    }
}
