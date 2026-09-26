<?php

declare(strict_types=1);

namespace Digitalocean\Services;

use Digitalocean\Enums\DropletActionType;
use Illuminate\Http\Client\ConnectionException;

class DropletActionsService
{
    public function __construct(protected DigitaloceanApi $digitaloceanApi)
    {
    }

    /**
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function list(int $dropletId, int $perPage = 20, int $page = 1): array
    {
        return $this->digitaloceanApi->send('GET', DigitaloceanApi::endpoint('droplets.actions', $dropletId), [
            'per_page' => $perPage,
            'page' => $page,
        ]);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function show(int $dropletId, int $actionId): array
    {
        return $this->digitaloceanApi->send('GET', DigitaloceanApi::endpoint('droplets.actions', $dropletId)."/{$actionId}");
    }

    /**
     * @param  array<string, mixed>  $params  Extra action attributes, e.g. `['name' => 'new-name']` for a rename.
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function initiate(int $dropletId, DropletActionType|string $type, array $params = []): array
    {
        return $this->digitaloceanApi->send('POST', DigitaloceanApi::endpoint('droplets.actions', $dropletId), [
            'type' => $type instanceof DropletActionType ? $type->value : $type,
            ...$params,
        ]);
    }

    /** @return array<string, mixed> */
    public function powerOn(int $dropletId): array
    {
        return $this->initiate($dropletId, DropletActionType::PowerOn);
    }

    /** @return array<string, mixed> */
    public function powerOff(int $dropletId): array
    {
        return $this->initiate($dropletId, DropletActionType::PowerOff);
    }

    /** @return array<string, mixed> */
    public function powerCycle(int $dropletId): array
    {
        return $this->initiate($dropletId, DropletActionType::PowerCycle);
    }

    /** @return array<string, mixed> */
    public function shutdown(int $dropletId): array
    {
        return $this->initiate($dropletId, DropletActionType::Shutdown);
    }

    /** @return array<string, mixed> */
    public function reboot(int $dropletId): array
    {
        return $this->initiate($dropletId, DropletActionType::Reboot);
    }

    /** @return array<string, mixed> */
    public function rename(int $dropletId, string $name): array
    {
        return $this->initiate($dropletId, DropletActionType::Rename, ['name' => $name]);
    }

    /** @return array<string, mixed> */
    public function resize(int $dropletId, string $size, bool $disk = false): array
    {
        return $this->initiate($dropletId, DropletActionType::Resize, ['size' => $size, 'disk' => $disk]);
    }

    /** @return array<string, mixed> */
    public function snapshot(int $dropletId, ?string $name = null): array
    {
        return $this->initiate($dropletId, DropletActionType::Snapshot, array_filter(['name' => $name]));
    }
}
