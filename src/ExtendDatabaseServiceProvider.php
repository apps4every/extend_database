<?php

namespace Apps4every\ExtendDatabase;

use Illuminate\Support\ServiceProvider;

class ExtendDatabaseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

        $this->loadRoutesFrom( __DIR__ . '/../routes/routes.php');
        $this->loadMigrationsFrom( __DIR__ . '/../database/migrations');
        $this->loadViewsFrom( __DIR__ . '/../resources/views', 'apps4every-extend_database');
        

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/apps4every_extend_database.php' => config_path('apps4every_extend_database.php'),
            ], 'apps4every_extend_database_config');
        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // $this->app->make('Apps4every\ExtendDatabase\Http\Controllers\XXXController');   
    }
}
