<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Core\Interfaces\HttpResponseAdapterInterface;
use App\Core\Interfaces\ViewsHandlerInterface;
use App\Core\Interfaces\ContainerInterface;
use App\Core\Interfaces\HttpControllerInterface;
use App\Core\ErrorHandler;
use App\Core\HttpResponse;
use App\Core\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Core\Traits\ContentTypeNegotiationTrait;
use App\Core\Traits\ErrorContentNegotiationTrait;

abstract class BaseController implements HttpControllerInterface
{
    use ErrorContentNegotiationTrait;
    use ContentTypeNegotiationTrait;

    protected ErrorHandler $errorHandler;
    protected ViewsHandlerInterface $viewsHandler;

    /**
     * The Base Controller
     *
     * @param ContainerInterface $container
     */
    public function __construct(protected ContainerInterface $container)
    {
        $this->errorHandler = $this->container->getErrorHandler();
        $this->viewsHandler = $this->container->getViewsHandler();
    }

    /**
     * Perform content negotiation for the current HTTP response
     *
     * @param ServerRequestInterface $request
     * @param HttpResponse $response
     * @return void
     */
    protected function negotiateOutputContent(ServerRequestInterface $request, HttpResponse &$response): void
    {
        // TODO: run content negotiation here (use a service)
    }

    /**
     * Respond as JSON
     *
     * @param ResponseInterface $response
     * @param array $data
     * @return ResponseInterface
     */
    protected function json(ResponseInterface $response, array $data = []): ResponseInterface
    {
        $response->withBody(json_encode($data) ?? '{}');
        return $response;
    }

    protected function view(string $name, ResponseInterface $response, array $data = []): ResponseInterface
    {
        $body = $this->viewsHandler->render($name, $data);
        $response->withBody($body);
        return $response;
    }

    /**
     * @inheritDoc
     */
    public function notFoundAction(ServerRequestInterface $request, HttpResponseAdapterInterface $response): ResponseInterface
    {
        $arguments = $request->getQueryParams();
        $keySegment = $arguments['keySegment'];

        if ($keySegment === Router::SEGMENT_WEB) {
            return $this->view('error/404', $response);
        }

        $response->withBody(match ($response->getHeader(Router::CONTENT_TYPE)) {
            Router::ACCEPTABLE_CONTENT_TYPES[1], Router::ACCEPTABLE_CONTENT_TYPES[2] => $this->getErrorAsXml(array_values(Router::ERROR_DETAIL_404)),
            Router::ACCEPTABLE_CONTENT_TYPES[3] => $this->getErrorAsJson(array_values(Router::ERROR_DETAIL_404))
        });

        return $response;
    }

    /**
     * Get a string version of this object
     *
     * @return string
     */
    public function __toString()
    {
        return get_class($this);
    }
}
