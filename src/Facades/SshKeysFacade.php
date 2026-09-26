<?php

declare(strict_types=1);

namespace Digitalocean\Facades;

use Digitalocean\Services\SshKeysService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array list(int $perPage = 20, int $page = 1)
 * @method static array store(string $name, string $publicKey)
 * @method static array show(int|string $key)
 * @method static array update(int|string $key, string $name)
 * @method static array destroy(int|string $key)
 *
 * @see SshKeysService
 */
class SshKeysFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SshKeysService::class;
    }
}
