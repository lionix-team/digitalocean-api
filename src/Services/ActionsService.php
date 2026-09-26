<?php

declare(strict_types=1);

namespace Digitalocean\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Sleep;

class ActionsService
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
        return $this->digitaloceanApi->send('GET', DigitaloceanApi::endpoint('actions'), [
            'per_page' => $perPage,
            'page' => $page,
        ]);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function show(int $actionId): array
    {
        return $this->digitaloceanApi->send('GET', DigitaloceanApi::endpoint('actions')."/{$actionId}");
    }

    /**
     * Poll an action until it is no longer `in-progress` or the timeout is reached.
     *
     * Returns the last response. Check `$response['action']['status']`: `completed`, `errored`,
     * or still `in-progress` when the timeout was reached.
     *
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function waitFor(int $actionId, int $timeout = 600, int $interval = 5): array
    {
        $interval = max(1, $interval);
        $attempts = max(1, intdiv($timeout, $interval) + 1);

        for ($attempt = 1; ; $attempt++) {
            $response = $this->show($actionId);

            $status = $response['action']['status'] ?? null;

            if ($status !== 'in-progress' || $attempt >= $attempts) {
                return $response;
            }

            Sleep::for($interval)->seconds();
        }
    }

    /**
     * @param  array<string, mixed>  $response
     */
    public static function completed(array $response): bool
    {
        return ($response['action']['status'] ?? null) === 'completed';
    }
}
