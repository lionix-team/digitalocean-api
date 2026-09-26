<?php

declare(strict_types=1);

namespace Digitalocean\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

/**
 * Low level client for the DigitalOcean v2 REST API.
 *
 * Every call returns the decoded JSON body as an array, with the HTTP
 * status code added under the `status_code` key. API errors (4xx / 5xx)
 * are returned the same way, so callers can inspect `id` and `message`.
 */
class DigitaloceanApi
{
    /**
     * @param  array<string, mixed>  $params  Sent as the query string for GET/DELETE and as a JSON body otherwise.
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function send(string $method, string $uri, array $params = []): array
    {
        $method = strtoupper($method);
        $options = [];

        if ($params !== []) {
            $options[in_array($method, ['GET', 'HEAD', 'DELETE'], true) ? 'query' : 'json'] = $params;
        }

        $response = $this->request()->send($method, ltrim($uri, '/'), $options);

        $data = $response->json();

        return [
            ...(is_array($data) ? $data : []),
            'status_code' => $response->status(),
        ];
    }

    /**
     * Build a pre-configured pending request, e.g. for endpoints this package does not wrap yet.
     */
    public function request(): PendingRequest
    {
        $request = Http::baseUrl((string) config('digital-ocean.base_url'))
            ->withToken((string) config('digital-ocean.token'))
            ->acceptJson()
            ->asJson()
            ->timeout((int) config('digital-ocean.timeout', 30));

        $times = (int) config('digital-ocean.retry.times', 0);

        if ($times > 0) {
            $request->retry(
                $times,
                (int) config('digital-ocean.retry.sleep', 100),
                static fn (\Throwable $e): bool => $e instanceof ConnectionException
                    || ($e instanceof RequestException && ($e->response->serverError() || $e->response->status() === 429)),
                throw: false,
            );
        }

        return $request;
    }

    /**
     * Replace the `:dropletId` placeholder in a configured endpoint.
     */
    public static function endpoint(string $key, int|string|null $dropletId = null): string
    {
        $endpoint = (string) config("digital-ocean.endpoints.{$key}");

        return $dropletId === null ? $endpoint : str_replace(':dropletId', (string) $dropletId, $endpoint);
    }
}
