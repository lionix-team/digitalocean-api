<?php

namespace Digitalocean\Services;

use Illuminate\Support\Facades\Validator;

class DomainService
{
    /**
     * @var mixed|\Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application
     */
    protected mixed $domainsApiUrl;

    /**
     * @param \Digitalocean\Services\DigitaloceanApi $digitaloceanApi
     */
    public function __construct(protected DigitaloceanApi $digitaloceanApi)
    {
        $this->domainsApiUrl = config('digital-ocean.endpoints.domains');
    }

    /**
     * @return mixed|\stdClass
     * @throws \GuzzleHttp\Exception\GuzzleException|\JsonException
     */
    public function list(): mixed
    {
        return $this->digitaloceanApi->send('GET', $this->domainsApiUrl);
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

        if ($validator->fails()) {
            return $validator->errors();
        }

        return $this->digitaloceanApi->send('POST', $this->domainsApiUrl, $params);
    }

    /**
     * @param string $name
     *
     * @return mixed|\stdClass
     *
     * @throws \GuzzleHttp\Exception\GuzzleException|\JsonException
     */
    public function show(string $name): mixed
    {
        return $this->digitaloceanApi->send('GET', "{$this->domainsApiUrl}/{$name}");
    }

    /**
     * @param string $name
     *
     * @return mixed|\stdClass
     * @throws \GuzzleHttp\Exception\GuzzleException|\JsonException
     */
    public function destroy(string $name): mixed
    {
        return $this->digitaloceanApi->send('DELETE', "{$this->domainsApiUrl}/${name}");
    }

    /**
     * @return string[]
     */
    private function getStoreRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'ip_address' => 'nullable|string',
        ];
    }
}
