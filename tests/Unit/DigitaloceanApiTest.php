<?php

declare(strict_types=1);

namespace Tests\Unit;

use Digitalocean\Facades\DigitaloceanFacade;
use Digitalocean\Services\DigitaloceanApi;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DigitaloceanApiTest extends TestCase
{
    public function test_it_sends_authenticated_json_requests(): void
    {
        Http::fake(['*' => Http::response(['account' => ['status' => 'active']])]);

        $result = $this->app->make(DigitaloceanApi::class)->send('post', 'account', ['foo' => 'bar']);

        $this->assertSame(['account' => ['status' => 'active'], 'status_code' => 200], $result);

        Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
            && $request->url() === 'https://api.digitalocean.com/v2/account'
            && $request->hasHeader('Authorization', 'Bearer test-token')
            && $request->data() === ['foo' => 'bar']);
    }

    public function test_get_params_are_sent_as_query_string(): void
    {
        Http::fake(['*' => Http::response([])]);

        $this->app->make(DigitaloceanApi::class)->send('GET', 'droplets', ['page' => 2]);

        Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api.digitalocean.com/v2/droplets?page=2');
    }

    public function test_empty_responses_only_contain_the_status_code(): void
    {
        Http::fake(['*' => Http::response(null, 204)]);

        $this->assertSame(['status_code' => 204], $this->app->make(DigitaloceanApi::class)->send('DELETE', 'droplets/1'));
    }

    public function test_api_errors_are_returned_with_the_status_code(): void
    {
        Http::fake(['*' => Http::response(['id' => 'not_found', 'message' => 'The resource was not found.'], 404)]);

        $this->assertSame(
            ['id' => 'not_found', 'message' => 'The resource was not found.', 'status_code' => 404],
            $this->app->make(DigitaloceanApi::class)->send('GET', 'droplets/1'),
        );
    }

    public function test_failed_requests_are_retried_when_configured(): void
    {
        config(['digital-ocean.retry.times' => 2, 'digital-ocean.retry.sleep' => 0]);

        Http::fakeSequence()
            ->push(['message' => 'Server error'], 500)
            ->push(['ok' => true], 200);

        $this->assertSame(['ok' => true, 'status_code' => 200], $this->app->make(DigitaloceanApi::class)->send('GET', 'account'));
        Http::assertSentCount(2);
    }

    public function test_global_facade_exposes_every_service(): void
    {
        Http::fake(['*' => Http::response(['droplets' => []])]);

        $this->assertSame(['droplets' => [], 'status_code' => 200], DigitaloceanFacade::droplets()->list());
        $this->assertSame(['droplets' => [], 'status_code' => 200], DigitaloceanFacade::send('GET', 'droplets'));
    }
}
