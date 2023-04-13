<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Core\HttpResponse;
use App\Core\Interfaces\AppMiddlewareInterface;
use App\Core\Router;
use App\Core\Traits\ContentTypeNegotiationTrait;
use App\Core\Traits\ErrorContentNegotiationTrait;
use Laminas\Diactoros\Response as LaminasResponse;
use Psr\Http\Message\ResponseInterface;

abstract class BaseMiddleware implements AppMiddlewareInterface
{
    use ErrorContentNegotiationTrait;
    use ContentTypeNegotiationTrait;

    protected const DEFAULT_OUTPUT_CONTENT_TYPE = Router::ACCEPTABLE_CONTENT_TYPES[0];
    protected const ERROR_OUTPUT_CAPTION = 'YOU SHALL NOT PASS!!!';
    protected const ERROR_OUTPUT_MESSAGE = 'Resource is not allowed for public use.';
    protected const ERROR_OUTPUT = [
        self::ERROR_OUTPUT_CAPTION,
        self::ERROR_OUTPUT_MESSAGE,
    ];

    /**
     * Get an standard error HTTP response
     *
     * @param string $type
     * @param string $contentType
     * @param int $status
     * @return HttpResponse
     */
    protected function getErrorResponse(string $type, string $contentType, int $status): ResponseInterface
    {
        $response = (new LaminasResponse())->withStatus($status)
            ->withHeader(HttpResponse::HEADER_CONTENT_TYPE, $contentType)
            ->getBody()->write($this->getErrorBody($type, $contentType));
//        $response = $response->withBody($this->getErrorBody($type, $contentType));

        return $response
            ->withHeader(HttpResponse::HEADER_CONTENT_TYPE, $contentType)
            ->withStatus($status);
    }

    /**
     * @inheritDoc
     */
    public function getErrorBody(string $type, string $contentType): string
    {
        $error = array_merge([$type], self::ERROR_OUTPUT);

        return match ($contentType) {
            Router::ACCEPTABLE_CONTENT_TYPES[1], Router::ACCEPTABLE_CONTENT_TYPES[2] => $this->getErrorAsXml($error),
            Router::ACCEPTABLE_CONTENT_TYPES[3] => $this->getErrorAsJson($error),
            default => $this->getErrorAsHtml($error),
        };
    }
}
