<?php

declare(strict_types=1);

namespace Digitalocean\Facades;

use Digitalocean\Services\DomainRecordsService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array list(string $domain, ?string $type = null, ?string $name = null, int $perPage = 20, int $page = 1)
 * @method static array store(string $domain, array $params)
 * @method static array show(string $domain, int $recordId)
 * @method static array update(string $domain, int $recordId, array $params)
 * @method static array destroy(string $domain, int $recordId)
 *
 * @see DomainRecordsService
 */
class DomainRecordsFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return DomainRecordsService::class;
    }
}
