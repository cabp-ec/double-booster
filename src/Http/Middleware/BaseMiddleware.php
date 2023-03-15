<?php

namespace App\Http\Middleware;

use App\Interfaces\WorkerMiddlewareInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

abstract class BaseMiddleware implements WorkerMiddlewareInterface
{
    protected function __construct(private string $key, private WorkerMiddlewareInterface $successor)
    {
    }

    /**
     * @inheritDoc
     */
    final public function getParentKey(): string
    {
        return $this->key;
    }

    /**
     * @inheritDoc
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @inheritDoc
     */
    public function setParentKey(string $value): void
    {
        $this->key = $value;
    }

    /**
     * @inheritDoc
     */
    final public function setKey(string $value): void
    {
        $this->key = $value;
    }

    /**
     * This approach by using a template method pattern ensures you that
     * each subclass will not forget to call the successor
     */
    final public function handle(RequestInterface $request): ?string
    {
        $processed = $this->processing($request);

        if ($processed) {
            // the request has been processed by this handler => see the next
            $processed = $this->successor->handle($request);
        }

        return $processed;
    }

//    abstract protected function processing(RequestInterface $request): bool;

    /**
     * @inheritDoc
     */
    abstract public function process(
        ServerRequestInterface  $request,
        RequestHandlerInterface $handler
    ): ResponseInterface;
}
