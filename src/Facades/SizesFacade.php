<?php

declare(strict_types=1);

namespace Digitalocean\Facades;

use Digitalocean\Services\SizesService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array list(int $perPage = 200, int $page = 1)
 *
 * @see SizesService
 */
class SizesFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SizesService::class;
    }
}
