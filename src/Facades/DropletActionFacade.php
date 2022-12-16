<?php

namespace Digitalocean\Facades;

use Digitalocean\Services\DropletActionService;
use Illuminate\Support\Facades\Facade;

class DropletActionFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return DropletActionService::class;
    }
}
