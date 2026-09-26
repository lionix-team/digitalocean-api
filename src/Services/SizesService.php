<?php

declare(strict_types=1);

namespace Digitalocean\Services;

use Illuminate\Http\Client\ConnectionException;

class SizesService
{
    public function __construct(protected DigitaloceanApi $digitaloceanApi)
    {
    }

    /**
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function list(int $perPage = 200, int $page = 1): array
    {
        return $this->digitaloceanApi->send('GET', DigitaloceanApi::endpoint('sizes'), [
            'per_page' => $perPage,
            'page' => $page,
        ]);
    }
}
