<?php

$ds = DIRECTORY_SEPARATOR;
$path = __DIR__ . DIRECTORY_SEPARATOR;
$pathRoutes = $path . "routes$ds";
$pathRChains = $path . "responsibilityChains$ds";

return [
    'core' => [
        'router' => [
            'web' => include_once $pathRoutes . 'web.php',
            'api' => include_once $pathRoutes . 'api.php',
        ],
        '\Session' => [],
        '\Translation' => [],
    ],
    'middleware' => include_once $pathRChains . 'middleware.php',
    'services' => [
        '\FileService' => [],
        '\LoggerService' => [],
        '\StorageService' => [
            'onlineDrives' => include_once $path . 'onlineDrives.php',
            'chains' => include_once $pathRChains . 'storage.php',
        ],
        '\MailingService' => [],
        '\TaxationService' => [],
    ],
    'modules' => [
        '\CRM' => [],
        '\Warehousing' => [],
        '\Ecommerce' => [],
    ],
];
