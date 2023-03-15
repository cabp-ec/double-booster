<?php

return [
    'router' => [
        'actionSuffix' => 'Action',
        'paramOpenChar' => '{',
        'paramCloseChar' => '}',
    ],
    'resourcePath' => [
        'resources' => [
            'downloads' => 'downloads',
            'uploads' => 'uploads',
            'input' => 'input',
            'output' => 'output',
            'transactions' => [
                'cancelled' => 'cancelled',
                'failed' => 'failed',
                'success' => 'success',
            ],
        ],
    ],
    'services' => [
        'FlatFileQuickMart' => [
            'files' => [
                'customers' => 'customers.txt',
                'inventory' => 'inventory.txt',
                'transaction' => 'transaction_%s_%s.txt', // txn number, date
            ],
            'customerDetailDelimiter' => ':',
            'productDetailDelimiter' => ':',
            'detailItemDelimiter' => ',',
            'maxInvoiceDigits' => 4,
        ],
    ],
];
