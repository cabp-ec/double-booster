<?php

declare(strict_types=1);

namespace App\Http\Controllers\Rpc;

use App\Core\Interfaces\ContainerInterface;
use App\Core\Interfaces\HttpResponseAdapterInterface;
use App\Http\Controllers\BaseController;
use App\Services\RawDataService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class StateController extends BaseController
{
    /** @var RawDataService */
    private RawDataService $rawDataService;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->rawDataService = $this->container->getService('RawData');
    }

    /**
     * Action handler for the demo endpoint
     *
     * @param ServerRequestInterface $request
     * @param HttpResponseAdapterInterface $response
     * @return ResponseInterface
     */
    public function demoStateAction(ServerRequestInterface $request, HttpResponseAdapterInterface $response): ResponseInterface
    {
        $output = $this->rawDataService->fromFile('demo');
        $output['result'] = [
            'abstract' => 'Lorem ipsum dolor sit amet consequent.',
            'slides' => [
                [
                    'title' => 'Surveillance Antennas',
                    'abstract' => 'Lorem ipsum dolor sit amet consequent.',
                ],
                [
                    'title' => 'Escape From Alcatraz',
                    'abstract' => 'Lorem ipsum dolor sit amet consequent.',
                ],
            ],
        ];

        return $this->json($response, $output);
    }

    /**
     * Action handler for the POST demo endpoint
     *
     * @param ServerRequestInterface $request
     * @param HttpResponseAdapterInterface $response
     * @return ResponseInterface
     */
    public function contactAction(ServerRequestInterface $request, HttpResponseAdapterInterface $response): ResponseInterface
    {
        $data = $request->getParsedBody();
        // TODO: do your "contact me" magic here

        $output = $this->rawDataService->fromFile('demo');
        $output['result'] = [
            'success' => true,
            'processedData' => $data,
        ];

        return $this->json($response, $output);
    }
}
