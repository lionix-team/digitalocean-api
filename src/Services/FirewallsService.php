<?php

declare(strict_types=1);

namespace Digitalocean\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class FirewallsService
{
    public function __construct(protected DigitaloceanApi $digitaloceanApi)
    {
    }

    /**
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function list(int $perPage = 20, int $page = 1): array
    {
        return $this->digitaloceanApi->send('GET', DigitaloceanApi::endpoint('firewalls'), [
            'per_page' => $perPage,
            'page' => $page,
        ]);
    }

    /**
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     *
     * @throws ValidationException
     * @throws ConnectionException
     */
    public function store(array $params): array
    {
        $this->validate($params);

        return $this->digitaloceanApi->send('POST', DigitaloceanApi::endpoint('firewalls'), $params);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function show(string $firewallId): array
    {
        return $this->digitaloceanApi->send('GET', $this->url($firewallId));
    }

    /**
     * Replace the firewall definition. DigitalOcean requires the full object (name and all rules).
     *
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     *
     * @throws ValidationException
     * @throws ConnectionException
     */
    public function update(string $firewallId, array $params): array
    {
        $this->validate($params);

        return $this->digitaloceanApi->send('PUT', $this->url($firewallId), $params);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function destroy(string $firewallId): array
    {
        return $this->digitaloceanApi->send('DELETE', $this->url($firewallId));
    }

    /**
     * @param  array<int, array<string, mixed>>  $inbound
     * @param  array<int, array<string, mixed>>  $outbound
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function addRules(string $firewallId, array $inbound = [], array $outbound = []): array
    {
        return $this->digitaloceanApi->sendWithBody('POST', $this->url($firewallId).'/rules', $this->rules($inbound, $outbound));
    }

    /**
     * @param  array<int, array<string, mixed>>  $inbound
     * @param  array<int, array<string, mixed>>  $outbound
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function removeRules(string $firewallId, array $inbound = [], array $outbound = []): array
    {
        return $this->digitaloceanApi->sendWithBody('DELETE', $this->url($firewallId).'/rules', $this->rules($inbound, $outbound));
    }

    /**
     * Allow inbound traffic from an IP address or CIDR on the given ports.
     *
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function allowAddress(string $firewallId, string $address, string $ports = 'all', string $protocol = 'tcp'): array
    {
        return $this->addRules($firewallId, inbound: [$this->addressRule($address, $ports, $protocol)]);
    }

    /**
     * Remove an inbound rule previously added with {@see allowAddress()}.
     *
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function revokeAddress(string $firewallId, string $address, string $ports = 'all', string $protocol = 'tcp'): array
    {
        return $this->removeRules($firewallId, inbound: [$this->addressRule($address, $ports, $protocol)]);
    }

    /**
     * @param  array<int, int>  $dropletIds
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function addDroplets(string $firewallId, array $dropletIds): array
    {
        return $this->digitaloceanApi->sendWithBody('POST', $this->url($firewallId).'/droplets', ['droplet_ids' => array_values($dropletIds)]);
    }

    /**
     * @param  array<int, int>  $dropletIds
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function removeDroplets(string $firewallId, array $dropletIds): array
    {
        return $this->digitaloceanApi->sendWithBody('DELETE', $this->url($firewallId).'/droplets', ['droplet_ids' => array_values($dropletIds)]);
    }

    /**
     * @param  array<int, string>  $tags
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function addTags(string $firewallId, array $tags): array
    {
        return $this->digitaloceanApi->sendWithBody('POST', $this->url($firewallId).'/tags', ['tags' => array_values($tags)]);
    }

    /**
     * @param  array<int, string>  $tags
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     */
    public function removeTags(string $firewallId, array $tags): array
    {
        return $this->digitaloceanApi->sendWithBody('DELETE', $this->url($firewallId).'/tags', ['tags' => array_values($tags)]);
    }

    protected function url(string $firewallId): string
    {
        return DigitaloceanApi::endpoint('firewalls')."/{$firewallId}";
    }

    /**
     * @return array<string, mixed>
     */
    protected function addressRule(string $address, string $ports, string $protocol): array
    {
        return [
            'protocol' => $protocol,
            'ports' => $ports,
            'sources' => ['addresses' => [$address]],
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $inbound
     * @param  array<int, array<string, mixed>>  $outbound
     * @return array<string, mixed>
     */
    protected function rules(array $inbound, array $outbound): array
    {
        return array_filter([
            'inbound_rules' => $inbound,
            'outbound_rules' => $outbound,
        ]);
    }

    /**
     * @param  array<string, mixed>  $params
     *
     * @throws ValidationException
     */
    protected function validate(array $params): void
    {
        Validator::make($params, [
            'name' => ['required', 'string'],
            'inbound_rules' => ['nullable', 'array'],
            'inbound_rules.*.protocol' => ['required', 'in:tcp,udp,icmp'],
            'inbound_rules.*.sources' => ['required', 'array'],
            'outbound_rules' => ['nullable', 'array'],
            'outbound_rules.*.protocol' => ['required', 'in:tcp,udp,icmp'],
            'outbound_rules.*.destinations' => ['required', 'array'],
            'droplet_ids' => ['nullable', 'array'],
            'tags' => ['nullable', 'array'],
        ])->validate();
    }
}
