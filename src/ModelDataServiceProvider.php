<?php

declare(strict_types = 1);

namespace Centrex\ModelData;

use Illuminate\Support\ServiceProvider;

class ModelDataServiceProvider extends ServiceProvider
{
    /** Bootstrap the application services. */
    public function boot(): void
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'model-data');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'model-data');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('model-data.php'),
            ], 'model-data-config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/model-data'),
            ], 'model-data-views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/model-data'),
            ], 'model-data-assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/model-data'),
            ], 'model-data-lang');*/

            // Registering package commands.
            // $this->commands([]);
        }
    }

    /** Register the application services. */
    public function register(): void
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'model-data');

        // Register the main class to use with the facade
        $this->app->singleton('model-data', fn (): Data => new Data());
    }
}
