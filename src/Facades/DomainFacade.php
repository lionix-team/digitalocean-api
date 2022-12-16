<?php

namespace Digitalocean\Facades;

use Digitalocean\Services\DomainService;
use Illuminate\Support\Facades\Facade;

class DomainFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return DomainService::class;
    }
}
