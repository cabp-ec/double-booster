<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\FlatFileCrmService;
use Exception;
use App\Core\Container;
use App\Core\HttpRequest;
use App\Core\HttpResponse;
use App\Services\FileStorageService;
use App\Services\FlatFileQuickMartService;

class IndexController extends BaseController
{
    private ?FileStorageService $fileStorageService;
    private ?FlatFileCrmService $flatFileCrmService;
    private ?FlatFileQuickMartService $flatFileQuickMartService;

    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->fileStorageService = $this->services->get(FileStorageService::class);
        $this->flatFileCrmService = $this->services->get(FlatFileCrmService::class);
        $this->flatFileQuickMartService = $this->services->get(FlatFileQuickMartService::class);
    }

    /**
     * Index Action
     *
     * @param HttpRequest $request
     * @return HttpResponse
     * @throws Exception
     */
    public function indexAction(HttpRequest $request): HttpResponse
    {
        $status = 200;
        $output = 'Hello home...!';

        return $this->respond($output, $status, $request);
    }

    /**
     * API Documentation Action
     *
     * @param HttpRequest $request
     * @return HttpResponse
     * @throws Exception
     */
    public function apiDocumentationAction(HttpRequest $request): HttpResponse
    {
        $status = 200;
        $output = [
            'status' => 'To be implemented',
        ];

        return $this->respond($output, $status, $request);
    }

    /**
     * @param HttpRequest $request
     * @return HttpResponse
     * @throws Exception
     */
    public function getCustomersAction(HttpRequest $request): HttpResponse
    {
        $status = 200;
        $params = json_decode($request->getBody(), true); // TODO: use a stream instead
        $output = $this->flatFileCrmService->getCustomers($params['filters'] ?? []);

//        echo '<pre>';
//        var_dump($output);
//        exit;

        return $this->respond($output, $status, $request);
    }

    /**
     * Get customer profile
     *
     * @param HttpRequest $request
     * @return HttpResponse
     * @throws Exception
     */
    public function getCustomerProfileAction(HttpRequest $request): HttpResponse
    {
        $status = 200;
        $params = json_decode($request->getBody(), true); // TODO: use a stream instead
        $output = [];

        return $this->respond($output, $status, $request);
    }

    /**
     * Get products
     *
     * @param HttpRequest $request
     * @return HttpResponse
     * @throws Exception
     */
    public function getProductsAction(HttpRequest $request): HttpResponse
    {
        $status = 200;
        $params = json_decode($request->getBody(), true); // TODO: use a stream instead
        $output = $this->flatFileQuickMartService->getInventory();

        return $this->respond($output, $status, $request);
    }

    /**
     * Add products to cart
     *
     * @param HttpRequest $request
     * @return HttpResponse
     * @throws Exception
     */
    public function appendProductsAction(HttpRequest $request): HttpResponse
    {
        $params = json_decode($request->getBody(), true); // TODO: use a stream instead
        $result = $this->flatFileQuickMartService->addProductToCart($params['products']);
        $status = $result ? 200 : 500;

        return $this->respond($this->session->data(), $status, $request);
    }

    /**
     * Remove products from the cart
     *
     * @param HttpRequest $request
     * @return HttpResponse
     * @throws Exception
     */
    public function removeProductsAction(HttpRequest $request): HttpResponse
    {
        $params = json_decode($request->getBody(), true); // TODO: use a stream instead
        $result = $this->flatFileQuickMartService->removeProductFromCart($params['products']);
        $status = $result ? 200 : 500;
        $output = $this->session->data();

        return $this->respond($output, $status, $request);
    }

    /**
     * Empty the cart
     *
     * @param HttpRequest $request
     * @return HttpResponse
     * @throws Exception
     */
    public function flushCartAction(HttpRequest $request): HttpResponse
    {
        $params = json_decode($request->getBody(), true); // TODO: use a stream instead
        $pool = [];

        foreach ($this->session->cart as $id => $qty) {
            $pool[] = [$id => $qty];
        }

        $result = $this->flatFileQuickMartService->removeProductFromCart($pool);
        $status = $result ? 200 : 500;
        $this->session->cart = $result ? [] : $this->session->cart;
        $output = $this->session->data();

        return $this->respond($output, $status, $request);
    }

    /**
     * View the current state of the cart
     *
     * @param HttpRequest $request
     * @return HttpResponse
     * @throws Exception
     */
    public function viewCartAction(HttpRequest $request): HttpResponse
    {
        $status = 200;
        $params = json_decode($request->getBody(), true); // TODO: use a stream instead
        $output = $this->flatFileQuickMartService->transaction();

        return $this->respond($output, $status, $request);
    }

    /**
     * Checkout Action
     *
     * @param HttpRequest $request
     * @return HttpResponse
     * @throws Exception
     */
    public function checkoutAction(HttpRequest $request): HttpResponse
    {
        $status = 200;
        $params = json_decode($request->getBody(), true); // TODO: use a stream instead
        $output = $this->flatFileQuickMartService->saveTransactionFile(
            $params['customer'],
            floatval($params['payment']['value']),
            strtoupper($params['payment']['type']),
        );

        return $this->respond($output, $status, $request);
    }

    public function cancelTransactionsAction(HttpRequest $request): HttpResponse
    {
        $status = 200;
        $params = json_decode($request->getBody(), true); // TODO: use a stream instead
        $output = $this->flatFileQuickMartService->cancelTransactions($params['transactions']);

//        echo '<pre>';
//        var_dump($output);
//        exit;

        return $this->respond($output, $status, $request);
    }
}
