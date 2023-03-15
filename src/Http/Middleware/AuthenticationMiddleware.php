<?php

namespace App\Http\Middleware;

use App\Interfaces\WorkerMiddlewareInterface;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use App\Http\Response;

class AuthenticationMiddleware extends BaseMiddleware
{
    public function __construct(string $key, WorkerMiddlewareInterface $successor)
    {
        parent::__construct($key, $successor);
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // TODO: do your magic here...
        $results = false;

        if (!$results) {
            $response = new Response();
            $response->getBody()->write(json_encode(['auth' => 'you shall not pass!']));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(Response::HTTP_UNAUTHORIZED);
        }

        return $handler->handle($request);
    }
}
