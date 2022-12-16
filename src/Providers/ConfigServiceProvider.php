<?php

namespace Digitalocean\Providers;

use Digitalocean\Commands\DOSnapshotCommand;
use Digitalocean\Services\DigitaloceanApi;
use Digitalocean\Services\DigitaloceanService;
use Digitalocean\Services\DomainService;
use Digitalocean\Services\DropletActionService;
use Digitalocean\Services\DropletsService;
use Digitalocean\Services\SnapshotService;
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

        $this->app->singleton(DomainService::class, function ($app) {
            return new DomainService($app->make(DigitaloceanApi::class));
        });

        $this->app->singleton(DropletsService::class, function ($app) {
            return new DropletsService($app->make(DigitaloceanApi::class));
        });

        $this->app->singleton(SnapshotService::class, function ($app) {
            return new SnapshotService($app->make(DigitaloceanApi::class));
        });

        $this->app->singleton(DropletActionService::class, function ($app) {
            return new DropletActionService($app->make(DigitaloceanApi::class));
        });

        $this->app->singleton(DigitaloceanService::class, function ($app) {
            return new DigitaloceanService($app->make(DigitaloceanApi::class));
        });
    }
}
