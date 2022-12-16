<?php

namespace Digitalocean\Facades;

use Digitalocean\Services\SnapshotService;
use Illuminate\Support\Facades\Facade;

class SnapshotFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return SnapshotService::class;
    }
}
