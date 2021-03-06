<?php

namespace DigitaloceanApi\Services;

use Illuminate\Support\Facades\Validator;
use DigitaloceanApi\Services\SendRequestService;

class DomainService
{
    /**
     * @var $sendRequestService
     */
    protected $sendRequestService;

    /**
     * @var $domainsApiUrl
     */
    protected $domainsApiUrl;

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
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function index()
    {
        return $this->sendRequestService->send('GET', $this->domainsApiUrl);
    }

    /**
     * @param array $params
     *
     * @return \Illuminate\Support\MessageBag|mixed|\stdClass
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function store(array $params)
    {
        $validator = Validator::make($params, $this->getStoreRules());

        if($validator->fails()) {
            return $validator->errors();
        }

        return $this->sendRequestService->send('POST', $this->domainsApiUrl, ['json' => $params]);
    }

    /**
     * @param string $name
     *
     * @return mixed|\stdClass
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function show(string $name)
    {
        return $this->sendRequestService->send('GET', "{$this->domainsApiUrl}/{$name}");
    }

    /**
     * @param string $name
     *
     * @return mixed|\stdClass

     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function destroy(string $name)
    {
        return $this->sendRequestService->send('DELETE', "{$this->domainsApiUrl}/${name}");
    }

    /**
     * @return string[]
     */
    private function getStoreRules()
    {
        return [
            'name' => 'required|string|max:255',
            'ip_address' => 'nullable|string',
        ];
    }
}