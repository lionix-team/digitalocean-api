<?php

namespace Digitalocean\Services;

use Illuminate\Support\Facades\Validator;

class SnapshotService
{
    /**
     * @var mixed|\Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application
     */
    protected mixed $snapshotsApiUrl;

    /**
     * @param \Digitalocean\Services\DigitaloceanApi $digitaloceanApi
     */
    public function __construct(protected DigitaloceanApi $digitaloceanApi)
    {
        $this->snapshotsApiUrl = config('digital-ocean.endpoints.snapshots');
    }

    /**
     * @return mixed|\stdClass
     * @throws \GuzzleHttp\Exception\GuzzleException|\JsonException
     */
    public function list(int $dropletId): mixed
    {
        return $this->digitaloceanApi->send('GET',
            str_replace(':dropletId', $dropletId, config('digital-ocean.endpoints.droplets.snapshots'))
        );
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function make(int $dropletId, string $name = null)
    {
        return $this->digitaloceanApi->send('POST',
            str_replace(':dropletId', $dropletId, config('digital-ocean.endpoints.droplets.actions')),
            [
                'type' => 'snapshot',
                'name' => ($name ?: $dropletId) . '-' . now()->toDateTimeString(),
            ]
        );
    }

    /**
     * @param string $snapshotId
     *
     * @return mixed|\stdClass
     *
     * @throws \GuzzleHttp\Exception\GuzzleException|\JsonException
     */
    public function show(string $snapshotId): mixed
    {
        return $this->digitaloceanApi->send('GET', "{$this->snapshotsApiUrl}/{$snapshotId}");
    }

    /**
     * @param string $snapshotId
     *
     * @return mixed|\stdClass
     * @throws \GuzzleHttp\Exception\GuzzleException|\JsonException
     */
    public function destroy(string $snapshotId): mixed
    {
        return $this->digitaloceanApi->send('DELETE', "{$this->snapshotsApiUrl}/{$snapshotId}");
    }
}
