<?php

declare(strict_types=1);

namespace Digitalocean\Facades;

use Digitalocean\Services\SnapshotsService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array list(int $dropletId, int $perPage = 20, int $page = 1)
 * @method static array all(?string $resourceType = null, int $perPage = 20, int $page = 1)
 * @method static array make(int $dropletId, ?string $name = null)
 * @method static array show(int|string $snapshotId)
 * @method static array destroy(int|string $snapshotId)
 *
 * @see SnapshotsService
 */
class SnapshotsFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SnapshotsService::class;
    }
}
