<?php

declare(strict_types=1);

namespace Digitalocean\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class DomainsService
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
        return $this->digitaloceanApi->send('GET', DigitaloceanApi::endpoint('domains'), [
            'per_page' => $perPage,
            'page' => $page,
        ]);
    }

    /**
     * @param  array{name: string, ip_address?: string|null}  $params
     * @return array<string, mixed>
     *
     * @throws ValidationException
     * @throws ConnectionException
     */
    public function store(array $params): array
    {
        Validator::make($params, [
            'name' => ['required', 'string', 'max:253'],
            'ip_address' => ['nullable', 'ip'],
        ])->validate();

        return $this->digitaloceanApi->send('POST', DigitaloceanApi::endpoint('domains'), $params);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function show(string $name): array
    {
        return $this->digitaloceanApi->send('GET', DigitaloceanApi::endpoint('domains').'/'.rawurlencode($name));
    }

    /**
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function destroy(string $name): array
    {
        return $this->digitaloceanApi->send('DELETE', DigitaloceanApi::endpoint('domains').'/'.rawurlencode($name));
    }
}
