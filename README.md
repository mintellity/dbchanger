# dbchanger
Change your database with request header. Laravel 5.

### Installation

Require package in your composer.json:
```json
"mintellity/dbchanger" : "dev-master"
```

Publish config file:
```sh
php artisan vendor:publish --provider="Mintellity\DbChanger\DbChangerServiceProvider"
```

Make sure the right user is set in the dbchanger.php config file. Your config should look like this:
```php
<?php

return [
    // this are the environments where dbchanger should work on. The active environment is fetched by .env file.
    'envs' => [
        'local'
    ],
    // this is the header parameter's name
    'headerParameter' => 'ApiTestIdentifier',
    // some infos about your db connection. Make sure this user is allowed to create databases.
    'connection' => [
        'host' => env('DB_HOST_TEST', 'localhost'),
        'user' => env('DB_USERNAME_TEST', 'homestead'),
        'password' => env('DB_PASSWORD_TEST', 'secret'),
        'prefix' => 'paladalo_'
    ]
];
```

Register the route-middleware in Kernel.php:
```php
$routeMiddleware = [
  'db.changeable' => \Mintellity\DbChanger\DbChangerMiddleware::class,
  ...
]
```

### How to use it

Now you are ready to use the DbChanger package. All you need to do is adding a middleware to the routes, where you want to listen for the db-change header-parameter. For example do this:

```php
Route::group(['as' => 'api.', 'prefix' => 'api', 'namespace' => 'Api', 'middleware' => ['db.changeable']], function () {
// ...
});
```

If you send a request to one of this routes, you can add a header-parameter with the desired database to work on.

If you want to create a new database you have to add this route:
```php
Route::get('buildDatabase/{databaseName}/{forceCreate?}', [
        'as' => 'apitest.buildDatabase',
        'uses' => '\Mintellity\DbChanger\DbChangerController@buildDatabase'
    ]);
```
So if you run http://yourpage.app/buildDatabase/testdatabase1/true in your browser, the system will create a new database called testdatabase1 (prefix can be defined in the config file).
If you create a new database or you force create it, your seeds will be fired automatically.
