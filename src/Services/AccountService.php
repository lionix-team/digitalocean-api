<?php

declare(strict_types=1);

namespace Digitalocean\Services;

use Illuminate\Http\Client\ConnectionException;

class AccountService
{
    public function __construct(protected DigitaloceanApi $digitaloceanApi)
    {
    }

    /**
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function show(): array
    {
        return $this->digitaloceanApi->send('GET', DigitaloceanApi::endpoint('account'));
    }

    /**
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function balance(): array
    {
        return $this->digitaloceanApi->send('GET', DigitaloceanApi::endpoint('balance'));
    }
}
