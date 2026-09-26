<?php

declare(strict_types=1);

namespace Digitalocean\Facades;

use Digitalocean\Services\ReservedIpsService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array list(int $perPage = 20, int $page = 1)
 * @method static array store(array $params)
 * @method static array show(string $ip)
 * @method static array destroy(string $ip)
 * @method static array assign(string $ip, int $dropletId)
 * @method static array unassign(string $ip)
 *
 * @see ReservedIpsService
 */
class ReservedIpsFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ReservedIpsService::class;
    }
}
