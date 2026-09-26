<?php

declare(strict_types=1);

namespace Digitalocean\Services;

use Illuminate\Http\Client\ConnectionException;

class DigitaloceanService
{
    public function __construct(protected DigitaloceanApi $digitaloceanApi)
    {
    }

    /**
     * Send a raw request to any DigitalOcean API endpoint.
     *
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function send(string $method, string $uri, array $params = []): array
    {
        return $this->digitaloceanApi->send($method, $uri, $params);
    }

    public function droplets(): DropletsService
    {
        return app(DropletsService::class);
    }

    public function dropletActions(): DropletActionsService
    {
        return app(DropletActionsService::class);
    }

    public function domains(): DomainsService
    {
        return app(DomainsService::class);
    }

    public function snapshots(): SnapshotsService
    {
        return app(SnapshotsService::class);
    }

    public function account(): AccountService
    {
        return app(AccountService::class);
    }

    public function actions(): ActionsService
    {
        return app(ActionsService::class);
    }

    public function cdn(): CdnService
    {
        return app(CdnService::class);
    }

    public function domainRecords(): DomainRecordsService
    {
        return app(DomainRecordsService::class);
    }

    public function firewalls(): FirewallsService
    {
        return app(FirewallsService::class);
    }

    public function images(): ImagesService
    {
        return app(ImagesService::class);
    }

    public function regions(): RegionsService
    {
        return app(RegionsService::class);
    }

    public function reservedIps(): ReservedIpsService
    {
        return app(ReservedIpsService::class);
    }

    public function sizes(): SizesService
    {
        return app(SizesService::class);
    }

    public function sshKeys(): SshKeysService
    {
        return app(SshKeysService::class);
    }
}
