<?php

namespace DigitaloceanApi\Services;

use GuzzleHttp\Client;

class DomainService
{
    /**
     * @var $domainsApiUrl
     */
    protected $domainsApiUrl;

    /**
     * @var $client
     */
    protected $client;

    public function __construct()
    {
        $this->domainsApiUrl = config('digital-ocean.api-urls.domains');
        $this->client = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '. config('digital-ocean.token'),
        ]]);
    }

    /**
     * @return mixed
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function index()
    {
        $res = $this->client->request('GET', $this->domainsApiUrl);

        return $res->getBody()->getContents();
    }

    /**
     * @param array $params
     *
     * @return mixed
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function store(array $params)
    {
        $res = $this->client->request('POST', $this->domainsApiUrl, $params);

        return $res->getBody()->getContents();
    }

    /**
     * @param $name
     *
     * @return mixed
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function show($name)
    {
        $res = $this->client->request('GET', "$this->domainsApiUrl/${name}");

        return $res->getBody()->getContents();
    }

    /**
     * @param $name
     *
     * @return mixed
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function destroy($name)
    {
        $res = $this->client->request('DELETE', "$this->domainsApiUrl/${name}");

        return $res->getBody()->getContents();
    }
}