<?php

// .com/api/c/{version}

return [
    '/{version}' => [
        'GET' => [
            'protected' => false,
            'controller' => 'Index',
            'action' => 'index',
            'pipeline' => 'ROUTE',
        ],
    ],
    '/{version}/state' => [
        'GET' => [
            'protected' => false,
            'controller' => 'State',
            'action' => 'get',
            'pipeline' => 'ROUTE',
        ],
    ],
    '/{version}/state/{processGroup}' => [
        'GET' => [
            'protected' => false,
            'controller' => 'State',
            'action' => 'get',
            'pipeline' => 'ROUTE',
        ],
    ],
];
