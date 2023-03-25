<?php

declare(strict_types=1);

namespace App\Http\Controllers\Rpc;

use App\Core\HttpResponse;
use App\Http\Controllers\BaseController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class StateController extends BaseController
{
    /**
     * The get action
     *
     * @param ServerRequestInterface $request
     * @param HttpResponse $response
     * @return ResponseInterface
     */
    public function getAction(ServerRequestInterface  $request, ResponseInterface $response): ResponseInterface
    {
        $output = [
            'loader' => 40,
        ];

//        echo '<pre>';
//        echo 'StateController<br>';
//        var_dump($response->getHeaders());
//        exit;

        return $this->json($response, $output);
    }
}
