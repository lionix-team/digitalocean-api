<?php

declare(strict_types=1);

namespace Digitalocean\Services;

use Illuminate\Http\Client\ConnectionException;

class ImagesService
{
    public function __construct(protected DigitaloceanApi $digitaloceanApi)
    {
    }

    /**
     * @param  string|null  $type  `distribution` or `application`.
     * @param  bool|null  $private  Only the account's own images (snapshots, backups, custom images).
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function list(?string $type = null, ?bool $private = null, ?string $tagName = null, int $perPage = 20, int $page = 1): array
    {
        return $this->digitaloceanApi->send('GET', DigitaloceanApi::endpoint('images'), array_filter([
            'type' => $type,
            'private' => $private === null ? null : ($private ? 'true' : 'false'),
            'tag_name' => $tagName,
            'per_page' => $perPage,
            'page' => $page,
        ], static fn (mixed $value): bool => $value !== null));
    }

    /**
     * @param  int|string  $image  The image ID or slug (e.g. `ubuntu-24-04-x64`).
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function show(int|string $image): array
    {
        return $this->digitaloceanApi->send('GET', DigitaloceanApi::endpoint('images')."/{$image}");
    }

    /**
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function destroy(int $imageId): array
    {
        return $this->digitaloceanApi->send('DELETE', DigitaloceanApi::endpoint('images')."/{$imageId}");
    }
}
