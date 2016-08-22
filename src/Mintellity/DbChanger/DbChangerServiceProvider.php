<?php

namespace Mintellity\DbChanger;

use Illuminate\Support\ServiceProvider;

class DbChangerServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     */
    public function register()
    {
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/dbchanger.php' => config_path('dbchanger.php'),
        ]);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }
}
