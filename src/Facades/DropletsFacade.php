<?php

namespace Digitalocean\Facades;

use Digitalocean\Services\DropletsService;
use Illuminate\Support\Facades\Facade;

class DropletsFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return DropletsService::class;
    }
}
