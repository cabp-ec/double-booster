<?php

// TODO: Upgrade route definitions to use ENUMS to specify HTTP methods and middleware pipelines
// TODO: Implement protected routes

return [
    '/' => [
        'GET' => [
            'protected' => false,
            'controller' => 'Index',
            'action' => 'index',
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
    '/articles/{title}' => [
        'GET' => [
            'protected' => false,
            'controller' => 'Index',
            'action' => 'postRead',
            'pipeline' => 'ROUTE',
        ],
    ],
];
