<?php

namespace Digitalocean\Facades;

use Digitalocean\Services\DomainsService;
use Illuminate\Support\Facades\Facade;

class DomainsFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return DomainsService::class;
    }
}
