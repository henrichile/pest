<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Registrar helpers
        $this->app->singleton('mapbox', function () {
            return new \App\Helpers\MapboxHelper();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Cargar archivos de helpers si existen
        $helpersPath = app_path('Helpers');
        if (is_dir($helpersPath)) {
            foreach (glob($helpersPath . '/*.php') as $file) {
                require_once $file;
            }
        }
    }
}
