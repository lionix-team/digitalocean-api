<?php

namespace DigitaloceanApi\Services;

use DigitaloceanApi\Services\SendRequestService;

class DomainService
{
    /**
     * @var $sendRequestService
     */
    protected $sendRequestService;

    /**
     * @var $client
     */
    protected $client;

    /**
     * @param \DigitaloceanApi\Services\SendRequestService $sendRequestService
     */
    public function __construct(SendRequestService $sendRequestService)
    {
        $this->domainsApiUrl = config('digital-ocean.api-urls.domains');
        $this->sendRequestService = $sendRequestService;
    }

    /**
     * @return mixed|\stdClass
     */
    public function index()
    {
        return $this->sendRequestService->send('GET', $this->domainsApiUrl);
    }

    /**
     * @param array $params
     *
     * @return mixed|\stdClass
     */
    public function store(array $params)
    {
        return $this->sendRequestService->send('POST', $this->domainsApiUrl, ['json' => $params]);
    }

    /**
     * @param $name
     *
     * @return mixed|\stdClass
     */
    public function show($name)
    {
        return $this->sendRequestService->send('GET', "{$this->domainsApiUrl}/{$name}");
    }

    /**
     * @param $name
     *
     * @return mixed|\stdClass
     */
    public function destroy($name)
    {
        return $this->sendRequestService->send('DELETE', "{$this->domainsApiUrl}/${name}");
    }
}