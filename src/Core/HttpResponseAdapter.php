<?php

declare(strict_types=1);

namespace App\Core;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use App\Core\Traits\ContentTypeNegotiationTrait;
use App\Core\Interfaces\HttpResponseAdapterInterface;

class HttpResponseAdapter implements HttpResponseAdapterInterface
{
    use ContentTypeNegotiationTrait;

    public const HEADER_CONTENT_TYPE = 'Content-Type';

    private ResponseInterface $response;

    /**
     * HttpResponseAdapter is an adapter for the HTTP ResponseInterface
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     */
    public function __construct(ServerRequestInterface $request, ResponseInterface $response)
    {
        $this->response = $response;
        $this->withHeader(Router::CONTENT_TYPE, $this->getResponseContentType($request));
    }

    /** @inheritDoc */
    public function getProtocolVersion()
    {
        // TODO: Implement getProtocolVersion() method.
    }

    /** @inheritDoc */
    public function withProtocolVersion($version)
    {
        // TODO: Implement withProtocolVersion() method.
    }

    /** @inheritDoc */
    public function getHeaders(): array
    {
        return $this->response->getHeaders();
    }

    /** @inheritDoc */
    public function hasHeader($name)
    {
        // TODO: Implement hasHeader() method.
    }

    /** @inheritDoc */
    public function getHeader($name): string
    {
        return $this->response->getHeader($name)[0];
    }

    /** @inheritDoc */
    public function getHeaderLine($name)
    {
        // TODO: Implement getHeaderLine() method.
    }

    /** @inheritDoc */
    public function withHeader($name, $value): static
    {
        $this->response = $this->response->withHeader($name, $value);
        return $this;
    }

    /** @inheritDoc */
    public function withAddedHeader($name, $value)
    {
        // TODO: Implement withAddedHeader() method.
    }

    /** @inheritDoc */
    public function withoutHeader($name)
    {
        // TODO: Implement withoutHeader() method.
    }

    /** @inheritDoc */
    public function getBody()
    {
        // TODO: Implement getBody() method.
    }

    /** @inheritDoc */
    public function withBody(string|StreamInterface $body): static
    {
        if (is_string($body)) {
            $this->response->getBody()->write($body);
        } else {
            $this->response = $this->response->withBody($body);
        }

        return $this;
    }

    /** @inheritDoc */
    public function getStatusCode(): int
    {
        return $this->response->getStatusCode();
    }

    /** @inheritDoc */
    public function withStatus($code, $reasonPhrase = ''): static
    {
        $this->response = $this->response->withStatus($code);
        return $this;
    }

    /** @inheritDoc */
    public function getReasonPhrase()
    {
        // TODO: Implement getReasonPhrase() method.
    }

    public function send(): static
    {
        self::sendResponse($this->response);

        return $this;
    }

    /**
     * Send the given response as is
     *
     * @param ResponseInterface $response
     * @return void
     */
    static public function sendResponse(ResponseInterface $response): void
    {
        $headers = $response->getHeaders();
        $statusCode = $response->getStatusCode();

        foreach ($headers as $name => $value) {
            $replace = 0 === strcasecmp($name, self::HEADER_CONTENT_TYPE);
            header($name . ': ' . $value[0], $replace, $statusCode);
        }

        $version = $response->getProtocolVersion();
        $statusText = $response->getReasonPhrase();
        header(sprintf('HTTP/%s %s %s', $version, $statusCode, $statusText), true, $statusCode);
        echo $response->getBody();
    }
}
