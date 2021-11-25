<?php

namespace Services;

use GuzzleHttp\Client;

class DomainService
{
    public function index()
    {
        $headers = [
            'Content-Type' => 'application/json',
            'AccessToken' => 'key',
            'Authorization' => 'Bearer token',
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