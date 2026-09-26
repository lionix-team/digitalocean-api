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
 * @method static \Digitalocean\Services\AccountService account()
 * @method static \Digitalocean\Services\ActionsService actions()
 * @method static \Digitalocean\Services\CdnService cdn()
 * @method static \Digitalocean\Services\DomainRecordsService domainRecords()
 * @method static \Digitalocean\Services\FirewallsService firewalls()
 * @method static \Digitalocean\Services\ImagesService images()
 * @method static \Digitalocean\Services\RegionsService regions()
 * @method static \Digitalocean\Services\ReservedIpsService reservedIps()
 * @method static \Digitalocean\Services\SizesService sizes()
 * @method static \Digitalocean\Services\SshKeysService sshKeys()
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
