<?php

namespace Digitalocean\Facades;

use Digitalocean\Services\DigitaloceanService;
use Illuminate\Support\Facades\Facade;

class DigitaloceanFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return DigitaloceanService::class;
    }
}
