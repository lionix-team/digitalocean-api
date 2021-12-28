<?php

namespace DigitaloceanApi\Services;

use Illuminate\Support\Facades\Validator;
use DigitaloceanApi\Services\SendRequestService;

class DropletService
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
        $this->domainsApiUrl = config('digital-ocean.api-urls.droplets');
        $this->sendRequestService = $sendRequestService;
    }

    /**
     * @param int $perPage
     * @param int $page
     *
     * @return mixed|\stdClass
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function index(
        int $perPage = 20,
        int $page = 1
    ) {
        $repuestParams = [
            'json' =>[
                'per_page' => $perPage,
                'page' => $page,
            ],
        ];

        return $this->sendRequestService->send('GET', $this->domainsApiUrl, $repuestParams);
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
     * @param int $dropletId
     *
     * @return mixed|\stdClass
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function show(int $dropletId)
    {
        return $this->sendRequestService->send('GET', "{$this->domainsApiUrl}/{$dropletId}");
    }

    /**
     * @param int $dropletId
     *
     * @return mixed|\stdClass
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function destroy(int $dropletId)
    {
        return $this->sendRequestService->send('DELETE', "{$this->domainsApiUrl}/{$dropletId}");
    }

    /**
     * @return string[]
     */
    private function getStoreRules()
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