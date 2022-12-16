<?php

namespace Digitalocean\Services;

use Illuminate\Support\Facades\Validator;

/**
 * @property \Digitalocean\Services\DigitaloceanApi $digitaloceanApi
 */
class DropletsService
{
    protected string $domainsApiUrl;

    /**
     * @param \Digitalocean\Services\DigitaloceanApi $digitaloceanApi
     */
    public function __construct(protected DigitaloceanApi $digitaloceanApi)
    {
        $this->domainsApiUrl = config('digital-ocean.endpoints.droplets.index');
    }

    /**
     * @param int $perPage
     * @param int $page
     *
     * @return mixed|\stdClass
     *
     * @throws \GuzzleHttp\Exception\GuzzleException|\JsonException
     */
    public function list(
        int $perPage = 20,
        int $page = 1
    ): mixed
    {
        $params = [
            'per_page' => $perPage,
            'page' => $page,
        ];

        return $this->digitaloceanApi->send('GET', $this->domainsApiUrl, $params);
    }

    /**
     * @param array $params
     *
     * @return \Illuminate\Support\MessageBag|mixed|\stdClass
     *
     * @throws \GuzzleHttp\Exception\GuzzleException|\JsonException
     */
    public function store(array $params): mixed
    {
        $validator = Validator::make($params, $this->getStoreRules());

        if($validator->fails()) {
            return $validator->errors();
        }

        return $this->digitaloceanApi->send('POST', $this->domainsApiUrl, $params);
    }

    /**
     * @param int $dropletId
     *
     * @return mixed|\stdClass
     *
     * @throws \GuzzleHttp\Exception\GuzzleException|\JsonException
     */
    public function show(int $dropletId): mixed
    {
        return $this->digitaloceanApi->send('GET', "{$this->domainsApiUrl}/{$dropletId}");
    }

    /**
     * @param int $dropletId
     *
     * @return mixed|\stdClass
     *
     * @throws \GuzzleHttp\Exception\GuzzleException|\JsonException
     */
    public function destroy(int $dropletId): mixed
    {
        return $this->digitaloceanApi->send('DELETE', "{$this->domainsApiUrl}/{$dropletId}");
    }

    /**
     * @return string[]
     */
    private function getStoreRules(): array
    {
        return [
            'name' => 'required|string',
            'region' => 'required|string',
            'size' => 'required|string',
            'image' => 'required|regex:/^[a-zA-Z0-9\s]+$/',
            'ssh_keys' => 'nullable|array',
            'backups' => 'nullable|boolean',
            'ipv6' => 'nullable|boolean',
            'monitoring' => 'nullable|boolean',
            'tags' => 'nullable|array',
            'user_data' => 'nullable|string',
            'vpc_uuid' => 'nullable|string',
            'with_droplet_agent' => 'nullable|boolean',
        ];
    }
}
