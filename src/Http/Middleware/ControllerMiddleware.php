<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Core\HttpResponseAdapter;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ControllerMiddleware extends BaseMiddleware
{
    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return new HttpResponseAdapter($request);
    }
}
