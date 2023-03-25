<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Core\Traits\ContentTypeNegotiationTrait;
use App\Core\Traits\ErrorContentNegotiationTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use App\Core\HttpResponse;

final class AuthorizationMiddleware extends BaseMiddleware
{
    use ErrorContentNegotiationTrait;
    use ContentTypeNegotiationTrait;

    const ERROR_OUTPUT_TYPE = 'AUTHORIZATION';

    private bool $shallPass = false;

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $outputContentType = $this->getResponseContentType($request);

        // TODO: do your magic here
        $this->shallPass = true;

        if (!$this->shallPass) {
            return $this->getErrorResponse(
                self::ERROR_OUTPUT_TYPE,
                $outputContentType,
                HttpResponse::HTTP_UNAUTHORIZED
            );
        }

        return $handler->handle($request);
    }
}
