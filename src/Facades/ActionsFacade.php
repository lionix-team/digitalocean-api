<?php

declare(strict_types=1);

namespace Digitalocean\Facades;

use Digitalocean\Services\ActionsService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array list(int $perPage = 20, int $page = 1)
 * @method static array show(int $actionId)
 * @method static array waitFor(int $actionId, int $timeout = 600, int $interval = 5)
 *
 * @see ActionsService
 */
class ActionsFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ActionsService::class;
    }
}
