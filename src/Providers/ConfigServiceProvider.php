<?php

namespace DigitaloceanApi\Providers;

use Services\DomainService;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ConfigServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/digital-ocean.php' => config_path('digital-ocean.php'),
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/digital-ocean.php', 'digital-ocean'
        );

        $this->app->bind(DomainService::class);
    }
}