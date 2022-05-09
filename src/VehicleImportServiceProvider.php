<?php

namespace VladimirNikotin\AaVehicleImport;

use Illuminate\Support\ServiceProvider;

use VladimirNikotin\AaVehicleImport\Console\ImportVehicles;

class VehicleImportServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'aavehiclesimport');
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('aavehiclesimport.php'),
            ], 'config');

            $this->commands([
                ImportVehicles::class,
            ]);
        }
    }
}
