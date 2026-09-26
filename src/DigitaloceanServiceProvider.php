<?php

declare(strict_types=1);

namespace Digitalocean;

use Digitalocean\Commands\DOSnapshotCommand;
use Digitalocean\Services\DigitaloceanApi;
use Digitalocean\Services\DigitaloceanService;
use Digitalocean\Services\DomainsService;
use Digitalocean\Services\DropletActionsService;
use Digitalocean\Services\DropletsService;
use Digitalocean\Services\SnapshotsService;
use Illuminate\Support\ServiceProvider;

class DigitaloceanServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/digital-ocean.php', 'digital-ocean');

        $this->app->singleton(DigitaloceanApi::class);
        $this->app->singleton(DigitaloceanService::class);
        $this->app->singleton(DomainsService::class);
        $this->app->singleton(DropletsService::class);
        $this->app->singleton(DropletActionsService::class);
        $this->app->singleton(SnapshotsService::class);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/digital-ocean.php' => config_path('digital-ocean.php'),
            ], 'digital-ocean-config');

            $this->commands([
                DOSnapshotCommand::class,
            ]);
        }
    }
}
