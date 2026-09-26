<?php

declare(strict_types=1);

namespace Digitalocean\Services;

use Illuminate\Http\Client\ConnectionException;

class CdnService
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
        return $this->digitaloceanApi->send('GET', DigitaloceanApi::endpoint('cdn'), [
            'per_page' => $perPage,
            'page' => $page,
        ]);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function show(string $endpointId): array
    {
        return $this->digitaloceanApi->send('GET', DigitaloceanApi::endpoint('cdn')."/{$endpointId}");
    }

    /**
     * Purge cached files. Paths may use wildcards, e.g. `['assets/*', 'index.html']`; `['*']` purges everything.
     *
     * @param  array<int, string>  $files
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function purge(string $endpointId, array $files = ['*']): array
    {
        return $this->digitaloceanApi->sendWithBody('DELETE', DigitaloceanApi::endpoint('cdn')."/{$endpointId}/cache", [
            'files' => array_values($files),
        ]);
    }
}
