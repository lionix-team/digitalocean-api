<?php

namespace Digitalocean\Services;

use GuzzleHttp\Client;

class DigitaloceanApi
{
    public function __construct(protected Client $client)
    {
    }

    /**
     * @param $method
     * @param $uri
     * @param array $params
     *
     * @return mixed|\stdClass
     *
     * @throws \GuzzleHttp\Exception\GuzzleException|\JsonException
     */
    public function send($method, $uri, array $params = []): mixed
    {
        try {
            $res = $this->client->request($method, $uri, ['json' => $params]);

            if($method !== 'DELETE') {
                $response = \json_decode($res->getBody(), true, 512, JSON_THROW_ON_ERROR);
            }

            $response['status_code'] = $res->getStatusCode();

            return $response;

        } catch (\GuzzleHttp\Exception\RequestException $exception) {
            $error = \json_decode($exception->getResponse()->getBody(), true, 512, JSON_THROW_ON_ERROR);
            $error['status_code'] = $exception->getResponse()->getStatusCode();

            return $error;
        }
    }
}
