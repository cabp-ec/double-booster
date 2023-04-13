<?php

// Example: .com/api/c/{version}

return [
    '/{version}' => [
        'GET' => [
            'protected' => false,
            'controller' => 'Index',
            'action' => 'index',
            'pipeline' => 'ROUTE',
        ],
    ],
    '/{version}/demo/state' => [
        'GET' => [
            'protected' => false,
            'controller' => 'State',
            'action' => 'demoState',
            'pipeline' => 'ROUTE',
        ],
    ],
    '/{version}/contact' => [
        'POST' => [
            'protected' => false,
            'controller' => 'State',
            'action' => 'contact',
            'pipeline' => 'ROUTE',
        ],
    ],
];
