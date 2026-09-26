<?php

declare(strict_types=1);

namespace Digitalocean\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class DropletsService
{
    public function __construct(protected DigitaloceanApi $digitaloceanApi)
    {
    }

    /**
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function list(int $perPage = 20, int $page = 1, ?string $tagName = null): array
    {
        return $this->digitaloceanApi->send('GET', DigitaloceanApi::endpoint('droplets.index'), array_filter([
            'per_page' => $perPage,
            'page' => $page,
            'tag_name' => $tagName,
        ], static fn (mixed $value): bool => $value !== null));
    }

    /**
     * Create one droplet (`name`) or several at once (`names`).
     *
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     *
     * @throws ValidationException
     * @throws ConnectionException
     */
    public function store(array $params): array
    {
        Validator::make($params, [
            'name' => ['required_without:names', 'string'],
            'names' => ['required_without:name', 'array', 'max:10'],
            'names.*' => ['string'],
            'region' => ['nullable', 'string'],
            'size' => ['required', 'string'],
            'image' => ['required'],
            'ssh_keys' => ['nullable', 'array'],
            'backups' => ['nullable', 'boolean'],
            'ipv6' => ['nullable', 'boolean'],
            'monitoring' => ['nullable', 'boolean'],
            'tags' => ['nullable', 'array'],
            'user_data' => ['nullable', 'string'],
            'vpc_uuid' => ['nullable', 'string'],
            'with_droplet_agent' => ['nullable', 'boolean'],
        ])->validate();

        return $this->digitaloceanApi->send('POST', DigitaloceanApi::endpoint('droplets.index'), $params);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function show(int $dropletId): array
    {
        return $this->digitaloceanApi->send('GET', DigitaloceanApi::endpoint('droplets.index')."/{$dropletId}");
    }

    /**
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function destroy(int $dropletId): array
    {
        return $this->digitaloceanApi->send('DELETE', DigitaloceanApi::endpoint('droplets.index')."/{$dropletId}");
    }
}
