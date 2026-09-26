<?php

declare(strict_types=1);

namespace Digitalocean\Facades;

use Digitalocean\Services\DropletsService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array list(int $perPage = 20, int $page = 1, ?string $tagName = null)
 * @method static array store(array $params)
 * @method static array show(int $dropletId)
 * @method static array destroy(int $dropletId)
 *
 * @see DropletsService
 */
class DropletsFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return DropletsService::class;
    }
}
