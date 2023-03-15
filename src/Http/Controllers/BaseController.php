<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Core\Container;
use App\Core\HttpResponse;
use App\Core\Router;
use App\Core\Services;
use App\Core\Session;
use App\Interfaces\HttpControllerInterface;
use Exception;
use Psr\Http\Message\RequestInterface;

abstract class BaseController implements HttpControllerInterface
{
    // TODO: use enums instead of or in combination with these constants
    private const KEY_ACCEPT = 'Accept';
    private const KEY_CONTENT_TYPE = 'Content-Type';
    private const CONTENT_TYPE_JSON = 'application/json';

    protected Services $services;
    protected Session $session;

    public function __construct(protected Container $container)
    {
        $this->session = $this->container->sessionHandler();
        $this->services = $this->container->services();
    }

    /**
     * Get a proper response content type
     *
     * @param RequestInterface $request
     * @return string
     */
    private function getResponseContentType(RequestInterface $request): string
    {
        // TODO: perform content negotiation here (e.g. request as json, output as xml)
        $requestContentType = $request->getHeader(strtolower(self::KEY_ACCEPT));

        return empty($requestContentType)
            ? Router::DEFAULT_ACCEPT_HEADER
            : ($requestContentType[0] ?? Router::DEFAULT_ACCEPT_HEADER);
    }

    /**
     * Send an HTTP response
     *
     * @param string|array $body
     * @param int $status
     * @param RequestInterface $request
     * @return HttpResponse
     * @throws Exception
     */
    protected function respond(string|array $body, int $status, RequestInterface $request): HttpResponse
    {
        $headers[self::KEY_CONTENT_TYPE] = $this->getResponseContentType($request);
        $contents = is_array($body) ? json_encode($body) : $body;
        return (new HttpResponse($contents, $status, $headers))->send();
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function notFoundAction(RequestInterface $request): HttpResponse
    {
        $output = '404 Not found';
        return $this->respond($output, 404, $request);
    }
}
