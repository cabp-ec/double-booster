<?php

use App\Core\Environment;

return [
    'router' => [
        'actionSuffix' => Environment::get('ACTION_SUFFIX', 'Action'),
        'restActions' => [
            'C' => Environment::get('REST_ACTION_C', 'Create'),
            'R' => Environment::get('REST_ACTION_R', 'Read'),
            'U' => Environment::get('REST_ACTION_U', 'Update'),
            'D' => Environment::get('REST_ACTION_D', 'Delete'),
            'A' => Environment::get('REST_ACTION_A', 'All'),
            'F' => Environment::get('REST_ACTION_F', 'Filter'),
        ],
        'paramEncloseChars' => [
            Environment::get('PARAM_ENCLOSE_OPEN', '{'),
            Environment::get('PARAM_ENCLOSE_CLOSE', '}'),
        ],
    ],
];
