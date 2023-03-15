<?php

// TODO: use an enum to specify middleware pipelines

return [
    '/' => [
        'module' => 'Core',
        'method' => [
            'GET' => [
                'protected' => false,
                'controller' => 'Index',
                'action' => 'index',
                'middleware' => 'ROUTE',
            ],
        ],
    ],
    '/help' => [
        'module' => 'Core',
        'method' => [
            'GET' => [
                'protected' => false,
                'controller' => 'Index',
                'action' => 'help',
                'middleware' => 'ROUTE',
            ],
        ],
    ],
    '/login' => [
        'module' => 'Core',
        'method' => [
            'GET' => [
                'protected' => false,
                'controller' => 'Index',
                'action' => 'signIn',
                'middleware' => 'ROUTE',
            ],
//            'POST' => [
//                'controller' => 'Index',
//                'action' => 'signInSubmission',
//                'middleware' => 'FORM_SUBMISSION',
//            ],
        ],
    ],
    '/logout' => [
        'module' => 'Core',
        'method' => [
            'GET' => [
                'protected' => false,
                'controller' => 'Index',
                'action' => 'signOut',
                'middleware' => 'ROUTE',
            ],
        ],
    ],
    '/register' => [
        'module' => 'CRM',
        'method' => [
            'GET' => [
                'protected' => false,
                'controller' => 'Index',
                'action' => 'signUp',
                'middleware' => 'ROUTE',
            ],
        ],
    ],
    '/abandon' => [
        'module' => 'CRM',
        'method' => [
            'GET' => [
                'protected' => false,
                'controller' => 'Index',
                'action' => 'signDown',
                'middleware' => 'ROUTE',
            ],
        ],
    ],
    '/cart' => [
        'module' => 'CRM',
        'method' => [
            'GET' => [
                'protected' => false,
                'controller' => 'Index',
                'action' => 'signDown',
                'middleware' => 'ROUTE',
            ],
        ],
    ],
    '/checkout' => [
        'module' => 'CRM',
        'method' => [
            'GET' => [
                'protected' => false,
                'controller' => 'Index',
                'action' => 'signDown',
                'middleware' => 'ROUTE',
            ],
        ],
    ],
    '/admin' => [
        'module' => 'Core',
        'method' => [
            'GET' => [
                'protected' => false,
                'controller' => 'BackOffice',
                'action' => 'signIn',
                'middleware' => 'ROUTE',
            ],
        ],
    ],
    '/admin/logout' => [
        'module' => 'Core',
        'method' => [
            'GET' => [
                'protected' => true,
                'controller' => 'BackOffice',
                'action' => 'dashboard',
                'middleware' => 'ROUTE',
            ],
        ],
    ],
    '/back-office' => [
        'module' => 'Core',
        'method' => [
            'GET' => [
                'protected' => true,
                'controller' => 'BackOffice',
                'action' => 'dashboard',
                'middleware' => 'ROUTE',
            ],
        ],
    ],
];
