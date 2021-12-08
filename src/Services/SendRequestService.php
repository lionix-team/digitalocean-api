<?php

namespace DigitaloceanApi\Services;

use GuzzleHttp\Client;

class SendRequestService
{
    /**
     * @var $client
     */
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '. config('digital-ocean.token'),
        ]]);
    }

    /**
     * @param $method
     * @param $uri
     * @param $params
     *
     * @return mixed|\stdClass
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function send($method, $uri, $params = [])
    {
        try {
            $res = $this->client->request($method, $uri, $params);

            if($method !== 'DELETE') {
                $response = \json_decode($res->getBody());
                $response->status_code = $res->getStatusCode();

                return $response;
            }
            $response = new \stdClass();
            $response->status_code = $res->getStatusCode();
            
            return $response;

        } catch (\GuzzleHttp\Exception\RequestException $exception) {
            $error = \json_decode($exception->getResponse()->getBody());
            $error->status_code = $exception->getResponse()->getStatusCode();

            return $error;
        }
    }
}