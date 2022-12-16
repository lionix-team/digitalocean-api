<?php

namespace Digitalocean\Facades;

use Digitalocean\Services\SnapshotsService;
use Illuminate\Support\Facades\Facade;

class SnapshotsFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return SnapshotsService::class;
    }
}
