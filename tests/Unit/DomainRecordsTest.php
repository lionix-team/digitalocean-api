<?php

declare(strict_types=1);

namespace Tests\Unit;

use Digitalocean\Facades\DomainRecordsFacade;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class DomainRecordsTest extends TestCase
{
    private const BASE = 'https://api.digitalocean.com/v2/domains/example.com/records';

    public function test_list_with_filters(): void
    {
        Http::fake(['*' => Http::response(['domain_records' => []])]);

        DomainRecordsFacade::list('example.com', 'A', 'www.example.com');

        Http::assertSent(fn (Request $request): bool => $request->url()
            === self::BASE.'?type=A&name=www.example.com&per_page=20&page=1');
    }

    public function test_crud(): void
    {
        Http::fake(['*' => Http::response(['domain_record' => ['id' => 5]], 201)]);

        $record = ['type' => 'A', 'name' => 'www', 'data' => '1.2.3.4', 'ttl' => 3600];

        $this->assertSame(201, DomainRecordsFacade::store('example.com', $record)['status_code']);
        DomainRecordsFacade::show('example.com', 5);
        DomainRecordsFacade::update('example.com', 5, ['data' => '5.6.7.8']);
        DomainRecordsFacade::destroy('example.com', 5);

        Http::assertSent(fn (Request $r): bool => $r->method() === 'POST' && $r->url() === self::BASE && $r->data() === $record);
        Http::assertSent(fn (Request $r): bool => $r->method() === 'GET' && $r->url() === self::BASE.'/5');
        Http::assertSent(fn (Request $r): bool => $r->method() === 'PATCH' && $r->url() === self::BASE.'/5' && $r->data() === ['data' => '5.6.7.8']);
        Http::assertSent(fn (Request $r): bool => $r->method() === 'DELETE' && $r->url() === self::BASE.'/5');
    }

    public function test_store_validates_record_type(): void
    {
        Http::fake();

        $this->expectException(ValidationException::class);

        DomainRecordsFacade::store('example.com', ['type' => 'BOGUS', 'name' => 'www']);
    }

    public function test_missing_endpoint_in_published_config_falls_back_to_default(): void
    {
        // Simulates a config file published with 2.0, which has no `domain_records` key.
        config(['digital-ocean.endpoints' => ['domains' => 'domains']]);
        Http::fake(['*' => Http::response([])]);

        DomainRecordsFacade::show('example.com', 5);

        Http::assertSent(fn (Request $r): bool => $r->url() === self::BASE.'/5');
    }
}
