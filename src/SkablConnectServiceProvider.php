<?php

namespace ezavalishin\SkablConnect;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class SkablConnectServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/skabl-connect.php', 'skabl-connect');

        // Register the service the package provides.
        $this->app->singleton('skabl-connect', function (Application $app) {
            return new SkablConnect(
                config('skabl-connect.url', 'http://localhost'),
                config('skabl-connect.client_id', 1),
                config('skabl-connect.client_secret', 'secret'),
            );
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['skabl-connect'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__ . '/../config/skabl-connect.php' => config_path('skabl-connect.php'),
        ], 'skabl-connect.config');
    }
}
