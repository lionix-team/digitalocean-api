<?php

declare(strict_types=1);

namespace Tests\Unit;

use Digitalocean\Facades\CdnFacade;
use Digitalocean\Facades\FirewallsFacade;
use Digitalocean\Facades\ReservedIpsFacade;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class NetworkingTest extends TestCase
{
    private const API = 'https://api.digitalocean.com/v2/';

    public function test_firewall_rules_are_sent_as_json_body_even_for_delete(): void
    {
        Http::fake(['*' => Http::response(null, 204)]);

        FirewallsFacade::allowAddress('fw-1', '203.0.113.7', '22');
        FirewallsFacade::revokeAddress('fw-1', '203.0.113.7', '22');

        $rule = ['inbound_rules' => [['protocol' => 'tcp', 'ports' => '22', 'sources' => ['addresses' => ['203.0.113.7']]]]];

        Http::assertSent(fn (Request $r): bool => $r->method() === 'POST' && $r->url() === self::API.'firewalls/fw-1/rules' && $r->data() === $rule);
        Http::assertSent(fn (Request $r): bool => $r->method() === 'DELETE' && $r->url() === self::API.'firewalls/fw-1/rules' && $r->data() === $rule);
    }

    public function test_firewall_droplets_and_tags(): void
    {
        Http::fake(['*' => Http::response(null, 204)]);

        FirewallsFacade::addDroplets('fw-1', [1, 2]);
        FirewallsFacade::removeTags('fw-1', ['web']);

        Http::assertSent(fn (Request $r): bool => $r->method() === 'POST' && $r->url() === self::API.'firewalls/fw-1/droplets' && $r->data() === ['droplet_ids' => [1, 2]]);
        Http::assertSent(fn (Request $r): bool => $r->method() === 'DELETE' && $r->url() === self::API.'firewalls/fw-1/tags' && $r->data() === ['tags' => ['web']]);
    }

    public function test_firewall_store_validates_rules(): void
    {
        Http::fake();

        $this->expectException(ValidationException::class);

        FirewallsFacade::store(['name' => 'web', 'inbound_rules' => [['protocol' => 'http']]]);
    }

    public function test_reserved_ips(): void
    {
        Http::fake(['*' => Http::response(['action' => ['id' => 1]], 201)]);

        ReservedIpsFacade::store(['region' => 'nyc3']);
        ReservedIpsFacade::assign('45.55.96.47', 42);
        ReservedIpsFacade::unassign('45.55.96.47');

        Http::assertSent(fn (Request $r): bool => $r->method() === 'POST' && $r->url() === self::API.'reserved_ips' && $r->data() === ['region' => 'nyc3']);
        Http::assertSent(fn (Request $r): bool => $r->url() === self::API.'reserved_ips/45.55.96.47/actions' && $r->data() === ['type' => 'assign', 'droplet_id' => 42]);
        Http::assertSent(fn (Request $r): bool => $r->data() === ['type' => 'unassign']);
    }

    public function test_reserved_ip_store_needs_droplet_or_region(): void
    {
        Http::fake();

        $this->expectException(ValidationException::class);

        ReservedIpsFacade::store([]);
    }

    public function test_cdn_purge(): void
    {
        Http::fake(['*' => Http::response(null, 204)]);

        CdnFacade::purge('cdn-1', ['assets/*']);

        Http::assertSent(fn (Request $r): bool => $r->method() === 'DELETE' && $r->url() === self::API.'cdn/endpoints/cdn-1/cache'
            && $r->data() === ['files' => ['assets/*']]);
    }

    public function test_cdn_purge_command(): void
    {
        config(['digital-ocean.cdn_endpoint_id' => 'cdn-1']);
        Http::fake(['*' => Http::response(null, 204)]);

        $this->artisan('do:cdn-purge')->assertSuccessful();
        $this->artisan('do:cdn-purge', ['endpoint' => 'cdn-2', '--file' => ['css/*', 'js/*']])->assertSuccessful();

        Http::assertSent(fn (Request $r): bool => $r->url() === self::API.'cdn/endpoints/cdn-1/cache' && $r->data() === ['files' => ['*']]);
        Http::assertSent(fn (Request $r): bool => $r->url() === self::API.'cdn/endpoints/cdn-2/cache' && $r->data() === ['files' => ['css/*', 'js/*']]);
    }

    public function test_cdn_purge_command_fails_without_endpoint(): void
    {
        Http::fake();

        $this->artisan('do:cdn-purge')->assertFailed();

        Http::assertNothingSent();
    }
}
