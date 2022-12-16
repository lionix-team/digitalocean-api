<?php

namespace Digitalocean\Services;

class DropletActionService
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
    public function initiate(int $dropletId, string $type, array $params = [])
    {
        return $this->digitaloceanApi->send('POST',
            str_replace(':dropletId', $dropletId, config('digital-ocean.endpoints.droplets.actions')),
            [
                'type' => $type,
                ...$params,
            ]
        );
    }
}
