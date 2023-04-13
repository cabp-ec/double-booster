<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Core\Interfaces\HttpResponseAdapterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class IndexController extends WebBaseController
{
    /**
     * Action handler for the home page
     *
     * @param ServerRequestInterface $request
     * @param HttpResponseAdapterInterface $response
     * @return ResponseInterface
     */
    public function indexAction(ServerRequestInterface $request, HttpResponseAdapterInterface $response): ResponseInterface
    {
        $viewData = [
            'Hello',
            'world',
        ];

        return $this->view('home', $response, $viewData);
    }

    /**
     * Action handler for the articles page
     *
     * @param ServerRequestInterface $request
     * @param HttpResponseAdapterInterface $response
     * @return ResponseInterface
     */
    public function articlesIndexAction(ServerRequestInterface $request, HttpResponseAdapterInterface $response): ResponseInterface
    {
        $viewData = [
            'Articles Main Page',
            'Here\'s a sample page',
        ];

        return $this->view('articles/index', $response, $viewData);
    }

    /**
     * Action handler for the article reading page
     *
     * @param ServerRequestInterface $request
     * @param HttpResponseAdapterInterface $response
     * @return ResponseInterface
     */
    public function postReadAction(ServerRequestInterface $request, HttpResponseAdapterInterface $response): ResponseInterface
    {
        $queryParams = $request->getQueryParams();

        $viewData = [
            'This is a Post!',
            'The route type is: ' . $queryParams['keySegment'],
            'The friendly URL segment for this post is: <em>' . $queryParams['title'] . '</em>',
        ];

        return $this->view('articles/post', $response, $viewData);
    }
}
