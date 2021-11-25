<?php

namespace DigitaloceanApi\Services;

use GuzzleHttp\Client;

class DomainService
{
    public function index()
    {
        $digitaloceanToken = config('digital-ocean.token');
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer ${digitaloceanToken}",
        ];

        $client = new Client([
            'headers' => $headers
        ]);

        $res = $client->request('GET', 'https://api.digitalocean.com/v2/domains', []);

        return $res->getBody()->getContents();
    }

    public function store($params)
    {

    }

    public function show($name)
    {

    }

    public function destroy($name)
    {

    }
}