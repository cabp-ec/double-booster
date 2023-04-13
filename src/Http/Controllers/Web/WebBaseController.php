<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Core\Interfaces\ContainerInterface;
use App\Core\Interfaces\HttpResponseAdapterInterface;
use App\Http\Controllers\BaseController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class WebBaseController extends BaseController
{
    /**
     * Action handler for the custom 404 page
     *
     * @param ServerRequestInterface $request
     * @param HttpResponseAdapterInterface $response
     * @return ResponseInterface
     */
    public function notFoundAction(ServerRequestInterface $request, HttpResponseAdapterInterface $response): ResponseInterface
    {
        return $this->view('error/404', $response);
    }
}
