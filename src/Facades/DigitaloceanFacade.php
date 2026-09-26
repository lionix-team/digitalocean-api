<?php

declare(strict_types=1);

namespace Digitalocean\Facades;

use Digitalocean\Services\DigitaloceanService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array send(string $method, string $uri, array $params = [])
 * @method static \Digitalocean\Services\DropletsService droplets()
 * @method static \Digitalocean\Services\DropletActionsService dropletActions()
 * @method static \Digitalocean\Services\DomainsService domains()
 * @method static \Digitalocean\Services\SnapshotsService snapshots()
 *
 * @see DigitaloceanService
 */
class DigitaloceanFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return DigitaloceanService::class;
    }
}
