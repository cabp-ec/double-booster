<?php

return [
    'AUTH' => [
        'Authentication' => [],
        'Authorization' => [],
    ],
    'ROUTE_SECURITY' => [
        'Cors' => [],
        'AUTH' => 'AUTH',
        'ParameterValidation' => [],
    ],
    'ROUTE' => [
        'ROUTE_SECURITY' => 'ROUTE_SECURITY',
        'Controller' => [],
    ],
    'FORM_SUBMISSION' => [
        'ROUTE_SECURITY' => 'ROUTE_SECURITY',
        'Csrf' => [],
        'Controller' => [],
    ],
];
