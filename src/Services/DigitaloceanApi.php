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
     * Default endpoint paths, used when a key is missing from a published config file.
     */
    public const ENDPOINTS = [
        'account' => 'account',
        'actions' => 'actions',
        'balance' => 'customers/my/balance',
        'cdn' => 'cdn/endpoints',
        'domains' => 'domains',
        'domain_records' => 'domains/:domain/records',
        'droplets' => [
            'index' => 'droplets',
            'snapshots' => 'droplets/:dropletId/snapshots',
            'actions' => 'droplets/:dropletId/actions',
        ],
        'firewalls' => 'firewalls',
        'images' => 'images',
        'regions' => 'regions',
        'reserved_ips' => 'reserved_ips',
        'sizes' => 'sizes',
        'snapshots' => 'snapshots',
        'ssh_keys' => 'account/keys',
    ];

    /**
     * @param  array<string, mixed>  $params  Sent as the query string for GET/HEAD/DELETE and as a JSON body otherwise.
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function send(string $method, string $uri, array $params = []): array
    {
        $method = strtoupper($method);

        return $this->dispatch($method, $uri, $params, in_array($method, ['GET', 'HEAD', 'DELETE'], true) ? 'query' : 'json');
    }

    /**
     * Send the params as a JSON body regardless of the method (e.g. DELETE endpoints that expect a body).
     *
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function sendWithBody(string $method, string $uri, array $params = []): array
    {
        return $this->dispatch(strtoupper($method), $uri, $params, 'json');
    }

    /**
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    protected function dispatch(string $method, string $uri, array $params, string $as): array
    {
        $response = $this->request()->send($method, ltrim($uri, '/'), $params === [] ? [] : [$as => $params]);

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
     * Resolve a configured endpoint and fill its placeholders.
     *
     * @param  int|string|array<string, int|string>|null  $replace  A droplet ID for `:dropletId`, or a map of placeholder => value.
     */
    public static function endpoint(string $key, int|string|array|null $replace = null): string
    {
        $endpoint = (string) (config("digital-ocean.endpoints.{$key}") ?? data_get(self::ENDPOINTS, $key));

        if ($replace === null) {
            return $endpoint;
        }

        if (! is_array($replace)) {
            $replace = ['dropletId' => $replace];
        }

        foreach ($replace as $placeholder => $value) {
            $endpoint = str_replace(':'.$placeholder, rawurlencode((string) $value), $endpoint);
        }

        return $endpoint;
    }
}
