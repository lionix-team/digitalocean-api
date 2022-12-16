<?php

namespace Digitalocean\Facades;

use Digitalocean\Services\DropletActionsService;
use Illuminate\Support\Facades\Facade;

class DropletActionsFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return DropletActionsService::class;
    }
}
