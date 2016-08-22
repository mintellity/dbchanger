<?php

return [
    // this are the envs to enable this dbchanger package
    'envs' => [
        'local'
    ],

    // the header parameter to listen to
    'headerParameter' => 'ApiTestIdentifier',
    
    // the validation rules for database creation
    'databaseNameValidation' => 'required|alpha_num|max:30|min:1',
    
    // some infos about your db connection.
    // Make sure this user is allowed to create databases.
    'connection' => [
        'host' => env('DB_HOST_TEST', 'localhost'),
        'user' => env('DB_USERNAME_TEST', 'homestead'),
        'password' => env('DB_PASSWORD_TEST', 'secret'),
        'prefix' => 'yourDbPrefix_'
    ]
];