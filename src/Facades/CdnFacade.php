<?php

declare(strict_types=1);

namespace Digitalocean\Facades;

use Digitalocean\Services\CdnService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array list(int $perPage = 20, int $page = 1)
 * @method static array show(string $endpointId)
 * @method static array purge(string $endpointId, array $files = ['*'])
 *
 * @see CdnService
 */
class CdnFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return CdnService::class;
    }
}
