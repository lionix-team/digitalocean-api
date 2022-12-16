<?php

namespace Digitalocean\Providers;

use Digitalocean\Commands\DOSnapshotCommand;
use Digitalocean\Services\DigitaloceanApi;
use Digitalocean\Services\DigitaloceanService;
use Digitalocean\Services\DomainsService;
use Digitalocean\Services\DropletActionsService;
use Digitalocean\Services\DropletsService;
use Digitalocean\Services\SnapshotsService;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

class ConfigServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../../config/digital-ocean.php' => config_path('digital-ocean.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                DOSnapshotCommand::class,
            ]);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/digital-ocean.php', 'digital-ocean'
        );

        $this->app->singleton(DigitaloceanApi::class, function ($app) {
            $client = new Client([
                'base_uri' => config('digital-ocean.base_url'),
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer '. config('digital-ocean.token'),
                ]]);

            return new DigitaloceanApi($client);
        });

        $this->app->singleton(DomainsService::class, function ($app) {
            return new DomainsService($app->make(DigitaloceanApi::class));
        });

        $this->app->singleton(DropletsService::class, function ($app) {
            return new DropletsService($app->make(DigitaloceanApi::class));
        });

        $this->app->singleton(SnapshotsService::class, function ($app) {
            return new SnapshotsService($app->make(DigitaloceanApi::class));
        });

        $this->app->singleton(DropletActionsService::class, function ($app) {
            return new DropletActionsService($app->make(DigitaloceanApi::class));
        });

        $this->app->singleton(DigitaloceanService::class, function ($app) {
            return new DigitaloceanService($app->make(DigitaloceanApi::class));
        });
    }
}
