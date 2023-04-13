<?php

declare(strict_types=1);

namespace App\Core\Interfaces;

use App\Core\HttpResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface HttpControllerInterface
{
    /**
     * Return a 404 page or endpoint
     *
     * @param ServerRequestInterface $request
     * @param HttpResponseAdapterInterface $response
     * @return ResponseInterface
     */
    public function notFoundAction(ServerRequestInterface  $request, HttpResponseAdapterInterface $response): ResponseInterface;
}
