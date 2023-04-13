<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Core\HttpResponseAdapter;
use Laminas\Diactoros\Response as LaminasResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ControllerMiddleware extends BaseMiddleware
{
    /**
     * Process the CORS response, if any
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return void
     */
    private function processCors(ServerRequestInterface &$request, ResponseInterface &$response): void
    {
        $requestBody = $request->getParsedBody();

        if (!isset($requestBody['cors'])) {
            return;
        }

        $skip = $requestBody['cors']['skip'];

        foreach ($requestBody['cors']['withHeaders'] as $key => $value) {
            $response = $response->withHeader($key, $value);
        }

        unset($requestBody['cors']);
        $request = $request->withParsedBody($requestBody);

        if ($skip) {
            HttpResponseAdapter::sendResponse($response);
            exit(0);
        }
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = (new LaminasResponse())->withStatus(200);
        $this->processCors($request, $response);
        return $response;
    }
}
