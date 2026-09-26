<?php

declare(strict_types=1);

namespace Tests\Unit;

use Digitalocean\Facades\DomainsFacade;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class DomainsServiceTest extends TestCase
{
    public function test_list(): void
    {
        Http::fake(['*' => Http::response(['domains' => [['name' => 'example.com']]])]);

        $this->assertSame('example.com', DomainsFacade::list()['domains'][0]['name']);

        Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && $request->url() === 'https://api.digitalocean.com/v2/domains?per_page=20&page=1');
    }

    public function test_store(): void
    {
        Http::fake(['*' => Http::response(['domain' => ['name' => 'example.com']], 201)]);

        $result = DomainsFacade::store(['name' => 'example.com', 'ip_address' => '1.2.3.4']);

        $this->assertSame(201, $result['status_code']);
        Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
            && $request->data() === ['name' => 'example.com', 'ip_address' => '1.2.3.4']);
    }

    public function test_store_validates_params(): void
    {
        Http::fake();

        $this->expectException(ValidationException::class);

        try {
            DomainsFacade::store(['ip_address' => 'not-an-ip']);
        } finally {
            Http::assertNothingSent();
        }
    }

    public function test_show_and_destroy(): void
    {
        Http::fake(['*' => Http::response(null, 204)]);

        DomainsFacade::show('example.com');
        DomainsFacade::destroy('example.com');

        Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && $request->url() === 'https://api.digitalocean.com/v2/domains/example.com');
        Http::assertSent(fn (Request $request): bool => $request->method() === 'DELETE'
            && $request->url() === 'https://api.digitalocean.com/v2/domains/example.com');
    }
}
