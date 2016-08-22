# dbchanger
Change your database with request header. Laravel 5.

1. Require package in your composer.json: 
```json
"mintellity/dbchanger" : "0.*"
```

2. Publish config file:
```sh
php artisan vendor:publish --provider="Mintellity\DbChanger\DbChangerServiceProvider"
```

You config should look like this:
```php
<?php

return [
    // this are the environments where dbchanger should work on. The active environment is fetched by .env file.
    'envs' => [
        'local'
    ],
    'headerParameter' => 'ApiTestIdentifier',
    'connection' => [
        'host' => env('DB_HOST_TEST', 'localhost'),
        'user' => env('DB_USERNAME_TEST', 'homestead'),
        'password' => env('DB_PASSWORD_TEST', 'secret'),
        'prefix' => 'paladalo_'
    ]
];
```

3. Make sure the right user is set in the dbchanger.php config file

4. Register the route-middleware in Kernel.php:
```php
$routeMiddleware = [
  'db.changeable' => \Mintellity\DbChanger\DbChangerMiddleware::class,
  ...
]
```

Now you are ready to use the DbChanger package. All you need to do is adding a middleware to the routes, where you want to allow the db changes. For example do this:

```php
Route::group(['as' => 'api.', 'prefix' => 'api', 'namespace' => 'Api', 'middleware' => ['db.changeable']], function () {
// ...
});
```

If you send an request to one of this routes, you can add a header-parameter with the desired database to work on.

If you want to create a new database you have to add this route:
```php
Route::get('buildDatabase/{databaseName}/{forceCreate?}', [
        'as' => 'apitest.buildDatabase',
        'uses' => '\Mintellity\DbChanger\DbChangerController@buildDatabase'
    ]);
```
So if you run yourpage.app/buildDatabase/testdatabase1/true in your browser, the system will create a new database called testdatabase1 (prefix can be defined in the config file).
