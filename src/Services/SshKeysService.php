<?php

declare(strict_types=1);

namespace Digitalocean\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SshKeysService
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
        return $this->digitaloceanApi->send('GET', DigitaloceanApi::endpoint('ssh_keys'), [
            'per_page' => $perPage,
            'page' => $page,
        ]);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws ValidationException
     * @throws ConnectionException
     */
    public function store(string $name, string $publicKey): array
    {
        $params = ['name' => $name, 'public_key' => $publicKey];

        Validator::make($params, [
            'name' => ['required', 'string'],
            'public_key' => ['required', 'string'],
        ])->validate();

        return $this->digitaloceanApi->send('POST', DigitaloceanApi::endpoint('ssh_keys'), $params);
    }

    /**
     * @param  int|string  $key  The key ID or fingerprint.
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function show(int|string $key): array
    {
        return $this->digitaloceanApi->send('GET', DigitaloceanApi::endpoint('ssh_keys')."/{$key}");
    }

    /**
     * @param  int|string  $key  The key ID or fingerprint.
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function update(int|string $key, string $name): array
    {
        return $this->digitaloceanApi->send('PUT', DigitaloceanApi::endpoint('ssh_keys')."/{$key}", ['name' => $name]);
    }

    /**
     * @param  int|string  $key  The key ID or fingerprint.
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function destroy(int|string $key): array
    {
        return $this->digitaloceanApi->send('DELETE', DigitaloceanApi::endpoint('ssh_keys')."/{$key}");
    }
}
