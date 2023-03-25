<?php

return [
    'AUTH' => [
        'Authentication' => [],
        'Authorization' => [],
    ],
    'ROUTE_SECURITY' => [
        'Cors' => [],
        'AUTH' => 'AUTH',
//        'StuffValidation' => [],
    ],
    'ROUTE' => [
        'ROUTE_SECURITY' => 'ROUTE_SECURITY',
    ],
    'FORM_SUBMISSION' => [
        'ROUTE_SECURITY' => 'ROUTE_SECURITY',
        'Csrf' => [],
    ],
];
