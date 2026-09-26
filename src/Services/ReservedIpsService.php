<?php

declare(strict_types=1);

namespace Digitalocean\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ReservedIpsService
{
    public function __construct(protected DigitaloceanApi $digitaloceanApi)
    {
    }

    /**
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function list(int $perPage = 20, int $page = 1): array
    {
        return $this->digitaloceanApi->send('GET', DigitaloceanApi::endpoint('reserved_ips'), [
            'per_page' => $perPage,
            'page' => $page,
        ]);
    }

    /**
     * Reserve an IP and assign it to a droplet (`droplet_id`), or reserve it in a region (`region`).
     *
     * @param  array{droplet_id?: int, region?: string, project_id?: string}  $params
     * @return array<string, mixed>
     *
     * @throws ValidationException
     * @throws ConnectionException
     */
    public function store(array $params): array
    {
        Validator::make($params, [
            'droplet_id' => ['required_without:region', 'integer'],
            'region' => ['required_without:droplet_id', 'string'],
            'project_id' => ['nullable', 'string'],
        ])->validate();

        return $this->digitaloceanApi->send('POST', DigitaloceanApi::endpoint('reserved_ips'), $params);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function show(string $ip): array
    {
        return $this->digitaloceanApi->send('GET', DigitaloceanApi::endpoint('reserved_ips')."/{$ip}");
    }

    /**
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function destroy(string $ip): array
    {
        return $this->digitaloceanApi->send('DELETE', DigitaloceanApi::endpoint('reserved_ips')."/{$ip}");
    }

    /**
     * Assign (or move) the reserved IP to a droplet.
     *
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function assign(string $ip, int $dropletId): array
    {
        return $this->digitaloceanApi->send('POST', DigitaloceanApi::endpoint('reserved_ips')."/{$ip}/actions", [
            'type' => 'assign',
            'droplet_id' => $dropletId,
        ]);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function unassign(string $ip): array
    {
        return $this->digitaloceanApi->send('POST', DigitaloceanApi::endpoint('reserved_ips')."/{$ip}/actions", [
            'type' => 'unassign',
        ]);
    }
}
