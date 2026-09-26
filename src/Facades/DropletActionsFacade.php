<?php

declare(strict_types=1);

namespace Digitalocean\Facades;

use Digitalocean\Services\DropletActionsService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array list(int $dropletId, int $perPage = 20, int $page = 1)
 * @method static array show(int $dropletId, int $actionId)
 * @method static array initiate(int $dropletId, \Digitalocean\Enums\DropletActionType|string $type, array $params = [])
 * @method static array powerOn(int $dropletId)
 * @method static array powerOff(int $dropletId)
 * @method static array powerCycle(int $dropletId)
 * @method static array shutdown(int $dropletId)
 * @method static array reboot(int $dropletId)
 * @method static array rename(int $dropletId, string $name)
 * @method static array resize(int $dropletId, string $size, bool $disk = false)
 * @method static array snapshot(int $dropletId, ?string $name = null)
 *
 * @see DropletActionsService
 */
class DropletActionsFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return DropletActionsService::class;
    }
}
