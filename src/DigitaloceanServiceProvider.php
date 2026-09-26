<?php

declare(strict_types=1);

namespace Digitalocean;

use Digitalocean\Commands\DOCdnPurgeCommand;
use Digitalocean\Commands\DOSnapshotCommand;
use Digitalocean\Services\AccountService;
use Digitalocean\Services\ActionsService;
use Digitalocean\Services\CdnService;
use Digitalocean\Services\DigitaloceanApi;
use Digitalocean\Services\DigitaloceanService;
use Digitalocean\Services\DomainRecordsService;
use Digitalocean\Services\DomainsService;
use Digitalocean\Services\DropletActionsService;
use Digitalocean\Services\DropletsService;
use Digitalocean\Services\FirewallsService;
use Digitalocean\Services\ImagesService;
use Digitalocean\Services\RegionsService;
use Digitalocean\Services\ReservedIpsService;
use Digitalocean\Services\SizesService;
use Digitalocean\Services\SnapshotsService;
use Digitalocean\Services\SshKeysService;
use Illuminate\Support\ServiceProvider;

class DigitaloceanServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/digital-ocean.php', 'digital-ocean');

        $this->app->singleton(DigitaloceanApi::class);
        $this->app->singleton(AccountService::class);
        $this->app->singleton(ActionsService::class);
        $this->app->singleton(CdnService::class);
        $this->app->singleton(DigitaloceanService::class);
        $this->app->singleton(DomainRecordsService::class);
        $this->app->singleton(DomainsService::class);
        $this->app->singleton(DropletActionsService::class);
        $this->app->singleton(DropletsService::class);
        $this->app->singleton(FirewallsService::class);
        $this->app->singleton(ImagesService::class);
        $this->app->singleton(RegionsService::class);
        $this->app->singleton(ReservedIpsService::class);
        $this->app->singleton(SizesService::class);
        $this->app->singleton(SnapshotsService::class);
        $this->app->singleton(SshKeysService::class);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/digital-ocean.php' => config_path('digital-ocean.php'),
            ], 'digital-ocean-config');

            $this->commands([
                DOCdnPurgeCommand::class,
                DOSnapshotCommand::class,
            ]);
        }
    }
}
