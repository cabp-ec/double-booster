<?php

declare(strict_types=1);

namespace App\Core\Traits;

use App\Core\Router;
use Psr\Http\Message\ServerRequestInterface;

trait ContentTypeNegotiationTrait
{
    /**
     * Get the key route segment from the URI
     *
     * @param string $uri
     * @return string
     */
    private function getRouteKeySegment(string $uri): string
    {
        $segments = explode('/', $uri);
        array_shift($segments);

        if ($segments[0] === Router::SEGMENT_API) {
            return match ($segments[1]) {
                'r' => Router::SEGMENT_REST,
                'c' => Router::SEGMENT_RPC,
            };
        }

        return Router::SEGMENT_WEB;
    }

    /**
     * Get the proper content-type for the output content
     *
     * @param ServerRequestInterface $request
     * @return string
     */
    protected function getResponseContentType(ServerRequestInterface $request): string
    {
        $keySegment = $this->getRouteKeySegment($request->getUri()->getPath());
        $acceptHeader = $request->getHeader('accept');
        $acceptHeaders = explode(',', $acceptHeader[0]);
        $intersectionHeaders = array_intersect(Router::ACCEPTABLE_CONTENT_TYPES, $acceptHeaders);

        if (Router::SEGMENTS_CONTENT_NEGOTIABLE[$keySegment]) {
            return $intersectionHeaders[0] ?? Router::SEGMENTS_CONTENT_TYPES[$keySegment];
        }

        return Router::SEGMENTS_CONTENT_TYPES[$keySegment];
    }
}
