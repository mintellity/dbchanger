<?php

return [
    'envs' => [
        'local'
    ],
    'headerParameter' => 'ApiTestIdentifier',
    'connection' => [
        'host' => env('DB_HOST_TEST', 'localhost'),
        'user' => env('DB_USERNAME_TEST', 'homestead'),
        'password' => env('DB_PASSWORD_TEST', 'secret'),
        'prefix' => 'yourDbPrefix_'
    ]
];