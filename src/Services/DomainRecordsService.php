<?php

declare(strict_types=1);

namespace Digitalocean\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class DomainRecordsService
{
    public const TYPES = ['A', 'AAAA', 'CAA', 'CNAME', 'MX', 'NS', 'SOA', 'SRV', 'TXT'];

    public function __construct(protected DigitaloceanApi $digitaloceanApi)
    {
    }

    /**
     * @param  string|null  $type  Filter by record type, e.g. `A`.
     * @param  string|null  $name  Filter by fully qualified record name, e.g. `www.example.com`.
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function list(string $domain, ?string $type = null, ?string $name = null, int $perPage = 20, int $page = 1): array
    {
        return $this->digitaloceanApi->send('GET', $this->url($domain), array_filter([
            'type' => $type,
            'name' => $name,
            'per_page' => $perPage,
            'page' => $page,
        ], static fn (mixed $value): bool => $value !== null));
    }

    /**
     * @param  array<string, mixed>  $params  e.g. `['type' => 'A', 'name' => 'www', 'data' => '1.2.3.4', 'ttl' => 3600]`
     * @return array<string, mixed>
     *
     * @throws ValidationException
     * @throws ConnectionException
     */
    public function store(string $domain, array $params): array
    {
        Validator::make($params, [
            'type' => ['required', 'string', 'in:'.implode(',', self::TYPES)],
            ...$this->rules(),
        ])->validate();

        return $this->digitaloceanApi->send('POST', $this->url($domain), $params);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function show(string $domain, int $recordId): array
    {
        return $this->digitaloceanApi->send('GET', $this->url($domain)."/{$recordId}");
    }

    /**
     * Update only the given attributes of a record.
     *
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     *
     * @throws ValidationException
     * @throws ConnectionException
     */
    public function update(string $domain, int $recordId, array $params): array
    {
        Validator::make($params, [
            'type' => ['sometimes', 'string', 'in:'.implode(',', self::TYPES)],
            ...$this->rules(),
        ])->validate();

        return $this->digitaloceanApi->send('PATCH', $this->url($domain)."/{$recordId}", $params);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function destroy(string $domain, int $recordId): array
    {
        return $this->digitaloceanApi->send('DELETE', $this->url($domain)."/{$recordId}");
    }

    protected function url(string $domain): string
    {
        return DigitaloceanApi::endpoint('domain_records', ['domain' => $domain]);
    }

    /**
     * @return array<string, array<int, string>>
     */
    protected function rules(): array
    {
        return [
            'name' => ['nullable', 'string'],
            'data' => ['nullable', 'string'],
            'priority' => ['nullable', 'integer'],
            'port' => ['nullable', 'integer'],
            'ttl' => ['nullable', 'integer', 'min:30'],
            'weight' => ['nullable', 'integer'],
            'flags' => ['nullable', 'integer'],
            'tag' => ['nullable', 'string'],
        ];
    }
}
