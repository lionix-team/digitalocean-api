<?php

declare(strict_types=1);

namespace Digitalocean\Services;

use Illuminate\Http\Client\ConnectionException;

class DigitaloceanService
{
    public function __construct(protected DigitaloceanApi $digitaloceanApi)
    {
    }

    /**
     * Send a raw request to any DigitalOcean API endpoint.
     *
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function send(string $method, string $uri, array $params = []): array
    {
        return $this->digitaloceanApi->send($method, $uri, $params);
    }

    public function droplets(): DropletsService
    {
        return app(DropletsService::class);
    }

    public function dropletActions(): DropletActionsService
    {
        return app(DropletActionsService::class);
    }

    public function domains(): DomainsService
    {
        return app(DomainsService::class);
    }

    public function snapshots(): SnapshotsService
    {
        return app(SnapshotsService::class);
    }
}
