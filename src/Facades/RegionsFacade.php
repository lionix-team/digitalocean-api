<?php

declare(strict_types=1);

namespace Digitalocean\Facades;

use Digitalocean\Services\RegionsService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array list(int $perPage = 200, int $page = 1)
 *
 * @see RegionsService
 */
class RegionsFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return RegionsService::class;
    }
}
