<?php

declare(strict_types=1);

namespace Digitalocean\Facades;

use Digitalocean\Services\ImagesService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array list(?string $type = null, ?bool $private = null, ?string $tagName = null, int $perPage = 20, int $page = 1)
 * @method static array show(int|string $image)
 * @method static array destroy(int $imageId)
 *
 * @see ImagesService
 */
class ImagesFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ImagesService::class;
    }
}
