<?php

declare(strict_types=1);

namespace Digitalocean\Services;

use Digitalocean\Enums\DropletActionType;
use Illuminate\Http\Client\ConnectionException;

class SnapshotsService
{
    public function __construct(protected DigitaloceanApi $digitaloceanApi)
    {
    }

    /**
     * List the snapshots of a droplet.
     *
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function list(int $dropletId, int $perPage = 20, int $page = 1): array
    {
        return $this->digitaloceanApi->send('GET', DigitaloceanApi::endpoint('droplets.snapshots', $dropletId), [
            'per_page' => $perPage,
            'page' => $page,
        ]);
    }

    /**
     * List all snapshots on the account, optionally filtered by `droplet` or `volume`.
     *
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function all(?string $resourceType = null, int $perPage = 20, int $page = 1): array
    {
        return $this->digitaloceanApi->send('GET', DigitaloceanApi::endpoint('snapshots'), array_filter([
            'resource_type' => $resourceType,
            'per_page' => $perPage,
            'page' => $page,
        ], static fn (mixed $value): bool => $value !== null));
    }

    /**
     * Take a snapshot of a droplet. The name defaults to "{name|dropletId}-{Y-m-d H:i:s}".
     *
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function make(int $dropletId, ?string $name = null): array
    {
        return $this->digitaloceanApi->send('POST', DigitaloceanApi::endpoint('droplets.actions', $dropletId), [
            'type' => DropletActionType::Snapshot->value,
            'name' => ($name ?: $dropletId).'-'.now()->toDateTimeString(),
        ]);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function show(int|string $snapshotId): array
    {
        return $this->digitaloceanApi->send('GET', DigitaloceanApi::endpoint('snapshots')."/{$snapshotId}");
    }

    /**
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function destroy(int|string $snapshotId): array
    {
        return $this->digitaloceanApi->send('DELETE', DigitaloceanApi::endpoint('snapshots')."/{$snapshotId}");
    }
}
