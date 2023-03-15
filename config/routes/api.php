<?php

// CRUD
// List
// Append
// TODO: find a better way to identify modules
// TODO: allow same route to handle different operations (i.e. crud) according to the method
// TODO: avoid duplication (DRY)

return [
    '/{version}' => [
        'module' => 'Core',
        'method' => [
            'GET' => [
                'protected' => true,
                'controller' => 'Index',
                'action' => 'apiDocumentation',
                'middleware' => 'ROUTE',
            ],
        ],
    ],

    '/{version}/crm/customers' => [
        'module' => 'Core',
        'method' => [
            'GET' => [
                'protected' => true,
                'controller' => 'Index',
                'action' => 'getCustomers',
                'middleware' => 'ROUTE',
            ],
        ],
    ],
    '/{version}/crm/customer/{code}' => [
        'module' => 'Core',
        'method' => [
            'GET' => [
                'protected' => true,
                'controller' => 'Index',
                'action' => 'getCustomerProfile',
                'middleware' => 'ROUTE',
            ],
        ],
    ],
    '/{version}/crm/customer/{code}/profile' => [
        'module' => 'Core',
        'method' => [
            'GET' => [
                'protected' => true,
                'controller' => 'Index',
                'action' => 'getCustomerProfile',
                'middleware' => 'ROUTE',
            ],
        ],
    ],

    '/{version}/ecommerce/products' => [
        'module' => 'Core',
        'method' => [
            'GET' => [
                'protected' => true,
                'controller' => 'Index',
                'action' => 'getProducts',
                'middleware' => 'ROUTE',
            ],
        ],
    ],
    '/{version}/ecommerce/cart' => [
        'module' => 'Core',
        'method' => [
            'POST' => [
                'protected' => true,
                'controller' => 'Index',
                'action' => 'viewCart',
                'middleware' => 'ROUTE',
            ],
        ],
    ],
    '/{version}/ecommerce/cart/throw-in' => [
        'module' => 'Core',
        'method' => [
            'POST' => [
                'protected' => true,
                'controller' => 'Index',
                'action' => 'appendProducts',
                'middleware' => 'ROUTE',
            ],
        ],
    ],
    '/{version}/ecommerce/cart/take-out' => [
        'module' => 'Core',
        'method' => [
            'POST' => [
                'protected' => true,
                'controller' => 'Index',
                'action' => 'removeProducts',
                'middleware' => 'ROUTE',
            ],
        ],
    ],
    '/{version}/ecommerce/cart/flush' => [
        'module' => 'Core',
        'method' => [
            'POST' => [
                'protected' => true,
                'controller' => 'Index',
                'action' => 'flushCart',
                'middleware' => 'ROUTE',
            ],
        ],
    ],
    '/{version}/ecommerce/checkout' => [
        'module' => 'Core',
        'method' => [
            'GET' => [
                'protected' => true,
                'controller' => 'Index',
                'action' => 'checkout',
                'middleware' => 'ROUTE',
            ],
        ],
    ],
    '/{version}/ecommerce/transaction/cancel' => [
        'module' => 'Core',
        'method' => [
            'GET' => [
                'protected' => true,
                'controller' => 'Index',
                'action' => 'cancelTransactions',
                'middleware' => 'ROUTE',
            ],
        ],
    ],

    '/{version}/ecommerce/cart/rem/{productId}/{qty}' => [
        'module' => 'Core',
        'method' => [
            'POST' => [
                'protected' => true,
                'controller' => 'Index',
                'action' => 'removeProductsAction',
                'middleware' => 'ROUTE',
            ],
        ],
    ],

    /*'v1' => [
        '/' => [
            'method' => 'GET',
            'controller' => 'Index',
            'action' => 'index',
        ],
        '/cart' => [
            'method' => 'GET',
            'controller' => 'Index',
            'action' => 'index',
        ],
        '/cart/add/{code}/{qty}/[promo]' => [
            'method' => 'GET',
            'controller' => 'Index',
            'action' => 'index',
        ],
    ],
    'v2' => [
        '/' => [
            'method' => 'GET',
            'controller' => 'Index',
            'action' => 'index',
        ],
    ],*/
];
