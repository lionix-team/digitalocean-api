<?php

declare(strict_types=1);

namespace Digitalocean\Facades;

use Digitalocean\Services\FirewallsService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array list(int $perPage = 20, int $page = 1)
 * @method static array store(array $params)
 * @method static array show(string $firewallId)
 * @method static array update(string $firewallId, array $params)
 * @method static array destroy(string $firewallId)
 * @method static array addRules(string $firewallId, array $inbound = [], array $outbound = [])
 * @method static array removeRules(string $firewallId, array $inbound = [], array $outbound = [])
 * @method static array allowAddress(string $firewallId, string $address, string $ports = 'all', string $protocol = 'tcp')
 * @method static array revokeAddress(string $firewallId, string $address, string $ports = 'all', string $protocol = 'tcp')
 * @method static array addDroplets(string $firewallId, array $dropletIds)
 * @method static array removeDroplets(string $firewallId, array $dropletIds)
 * @method static array addTags(string $firewallId, array $tags)
 * @method static array removeTags(string $firewallId, array $tags)
 *
 * @see FirewallsService
 */
class FirewallsFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return FirewallsService::class;
    }
}
