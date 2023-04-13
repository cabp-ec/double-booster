<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Core\HttpResponseAdapter;
use App\Core\Router;
use App\Core\Traits\ContentTypeNegotiationTrait;
use App\Core\Traits\ErrorContentNegotiationTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use App\Core\HttpResponse;

final class CorsMiddleware extends BaseMiddleware
{
    use ErrorContentNegotiationTrait;
    use ContentTypeNegotiationTrait;

    private const ERROR_OUTPUT_TYPE = 'CORS';
    private const VALID_METHODS = [Router::HTTP_METHOD_GET, Router::HTTP_METHOD_POST, Router::HTTP_METHOD_OPTIONS];
    private const ALLOWED_ORIGINS = [
        'http://parallel-booster.local'
    ];
    private const ALLOWED_HEADERS = [
        'Access-Control-Allow-Origin',
        'Accept',
        'Content-Type',
        'Content-Length',
        'Accept-Encoding',
        'Origin',
        // 'Accept-Origin',
        'X-CSRF-Token',
        'Authorization',
        // 'X-Requested-With',
    ];

    private bool $shallPass = false;

    /**
     * Update the server request
     *
     * @param ServerRequestInterface $request
     * @param string $origin
     * @return ServerRequestInterface
     */
    private function processRequest(ServerRequestInterface $request, string $origin): ServerRequestInterface
    {
        $body = $request->getParsedBody();

        $body['cors'] = [
            'withHeaders' => [
                'Access-Control-Allow-Origin' => $origin,
                'Access-Control-Allow-Methods' => implode(', ', self::VALID_METHODS),
                'Access-Control-Allow-Headers' => implode(', ', self::ALLOWED_HEADERS),
            ],
            'skip' => $request->getMethod() === Router::HTTP_METHOD_OPTIONS,
            'valid' => $this->shallPass,
        ];

        return $request->withParsedBody($body);
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $queryParams = $request->getQueryParams();

        if ($queryParams['keySegment'] === Router::SEGMENT_WEB) {
            return $handler->handle($request);
        }

        $host = $request->getHeader('host');
        $host = $host[0] ?? null;
        $origin = $request->getHeader('origin');
        $origin = $origin[0] ?? null;
        $origin = empty($origin) ? $host : $origin;
        $outputContentType = $this->getResponseContentType($request);
        $requestMethod = $request->getMethod();
        $originIntersect = array_intersect(self::ALLOWED_ORIGINS, [$origin]);
        $this->shallPass = in_array($requestMethod, self::VALID_METHODS) && count($originIntersect) === 1;

        if (!$this->shallPass) {
            HttpResponseAdapter::sendResponse($this->getErrorResponse(
                self::ERROR_OUTPUT_TYPE,
                $outputContentType,
                HttpResponse::HTTP_UNAUTHORIZED
            ));

            exit(0);
        }

        return $handler->handle($this->processRequest($request, array_values($originIntersect)[0] ?? ''));
    }
}
