<?php

$dataPath  = dirname(__DIR__) . DIRECTORY_SEPARATOR;
$dataPath .= 'database' . DIRECTORY_SEPARATOR . 'raw' . DIRECTORY_SEPARATOR;

return [
    'start' => [],
    'lazy' => [
        '\App\Services\RawDataService' => [
            'dataPath' => $dataPath,
        ]
    ],
];
