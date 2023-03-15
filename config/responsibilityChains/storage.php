<?php

return [
    'RELATIONAL' => [
        'App\DataStorage\MySqlManager' => [],
        'App\DataStorage\MsSqlManager' => [],
        'App\DataStorage\PsSqlManager' => [],
    ],
    'NON_RELATIONAL' => [
        'App\DataStorage\MongoDbManager' => [],
        'App\DataStorage\FirebaseManager' => [],
        'App\DataStorage\InFileStorageManager' => [],
    ],
    'IN_FILE' => [
        'App\DataStorage\DropBoxStorageManager' => [],
        'App\DataStorage\AwsS3StorageManager' => [],
        'App\DataStorage\LocalStorageManager' => [],
    ],
    'FULL_CHAIN' => [
        'RELATIONAL' => 'RELATIONAL',
        'NON_RELATIONAL' => 'NON_RELATIONAL',
        'IN_FILE' => 'IN_FILE',
    ],
];
