<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Core\HttpResponse;
use Psr\Http\Message\RequestInterface;

interface HttpControllerInterface
{
    /**
     * Return a 404 page or endpoint
     *
     * @param RequestInterface $request
     * @return HttpResponse
     */
    public function notFoundAction(RequestInterface $request): HttpResponse;
}
