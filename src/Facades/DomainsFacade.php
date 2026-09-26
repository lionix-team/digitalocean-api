<?php

declare(strict_types=1);

namespace Digitalocean\Facades;

use Digitalocean\Services\DomainsService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array list(int $perPage = 20, int $page = 1)
 * @method static array store(array $params)
 * @method static array show(string $name)
 * @method static array destroy(string $name)
 *
 * @see DomainsService
 */
class DomainsFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return DomainsService::class;
    }
}
