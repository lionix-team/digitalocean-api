<?php

declare(strict_types=1);

namespace Digitalocean\Facades;

use Digitalocean\Services\AccountService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array show()
 * @method static array balance()
 *
 * @see AccountService
 */
class AccountFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return AccountService::class;
    }
}
