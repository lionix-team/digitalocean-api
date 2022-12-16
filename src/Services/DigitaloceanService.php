<?php

namespace Digitalocean\Services;


class DigitaloceanService
{
    /**
     * @param \Digitalocean\Services\DigitaloceanApi $digitaloceanApi
     */
    public function __construct(protected DigitaloceanApi $digitaloceanApi)
    {

    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function send($method, $uri, array $params = [])
    {
        return $this->digitaloceanApi->send($method, $uri, $params);
    }

    public function droplets(): mixed
    {
        return app(DropletsService::class);
    }

    public function domains(): mixed
    {
        return app(DomainService::class);
    }

    public function dropletActions()
    {
        return app(DropletActionService::class);
    }

    public function snapshots()
    {
        return app(SnapshotService::class);
    }
}
