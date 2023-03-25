<?php

// .com/api/r/{version}
// .com/api/r/{version}/product

return [
    '/{version}' => [
        'protected' => false,
        'controller' => 'Index',
        'pipeline' => 'ROUTE',
    ],
    '/{version}/{entityName}' => [
        'protected' => false,
        'controller' => 'Index',
        'pipeline' => 'ROUTE',
    ],
];
