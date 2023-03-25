<?php

// TODO: upgrade and ENUMS to specify HTTP methods and middleware pipelines
// TODO: implement protected routes

return [
    '/' => [
        'GET' => [
            'protected' => false,
            'controller' => 'Index',
            'action' => 'index',
            'pipeline' => 'ROUTE',
        ],
    ],
    '/content-index' => [
        'GET' => [
            'protected' => false,
            'controller' => 'Index',
            'action' => 'contentIndex',
            'pipeline' => 'ROUTE',
        ],
    ],
    '/books' => [
        'GET' => [
            'protected' => false,
            'controller' => 'Index',
            'action' => 'booksIndex',
            'pipeline' => 'ROUTE',
        ],
    ],
    '/books/{friendlyName}' => [
        'GET' => [
            'protected' => false,
            'controller' => 'Index',
            'action' => 'bookDetail',
            'pipeline' => 'ROUTE',
        ],
    ],
    '/articles' => [
        'GET' => [
            'protected' => false,
            'controller' => 'Index',
            'action' => 'articlesIndex',
            'pipeline' => 'ROUTE',
        ],
    ],
    '/articles/{friendlyName}' => [
        'GET' => [
            'protected' => false,
            'controller' => 'Index',
            'action' => 'articleRead',
            'pipeline' => 'ROUTE',
        ],
    ],
];
