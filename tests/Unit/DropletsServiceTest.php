<?php

declare(strict_types=1);

namespace Tests\Unit;

use Digitalocean\Enums\DropletActionType;
use Digitalocean\Facades\DropletActionsFacade;
use Digitalocean\Facades\DropletsFacade;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class DropletsServiceTest extends TestCase
{
    public function test_list_with_tag_filter(): void
    {
        Http::fake(['*' => Http::response(['droplets' => []])]);

        DropletsFacade::list(50, 2, 'web');

        Http::assertSent(fn (Request $request): bool => $request->url()
            === 'https://api.digitalocean.com/v2/droplets?per_page=50&page=2&tag_name=web');
    }

    public function test_store_accepts_image_slugs(): void
    {
        Http::fake(['*' => Http::response(['droplet' => ['id' => 1]], 202)]);

        $params = ['name' => 'web-1', 'region' => 'nyc3', 'size' => 's-1vcpu-1gb', 'image' => 'ubuntu-24-04-x64'];

        $this->assertSame(202, DropletsFacade::store($params)['status_code']);
        Http::assertSent(fn (Request $request): bool => $request->method() === 'POST' && $request->data() === $params);
    }

    public function test_store_validates_params(): void
    {
        Http::fake();

        $this->expectException(ValidationException::class);

        DropletsFacade::store(['name' => 'web-1']);
    }

    public function test_show_and_destroy(): void
    {
        Http::fake(['*' => Http::response(null, 204)]);

        DropletsFacade::show(42);
        DropletsFacade::destroy(42);

        Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && $request->url() === 'https://api.digitalocean.com/v2/droplets/42');
        Http::assertSent(fn (Request $request): bool => $request->method() === 'DELETE'
            && $request->url() === 'https://api.digitalocean.com/v2/droplets/42');
    }

    public function test_droplet_actions(): void
    {
        Http::fake(['*' => Http::response(['action' => ['id' => 7]], 201)]);

        DropletActionsFacade::initiate(42, DropletActionType::Reboot);
        DropletActionsFacade::initiate(42, 'power_on');
        DropletActionsFacade::rename(42, 'renamed');
        DropletActionsFacade::show(42, 7);

        Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api.digitalocean.com/v2/droplets/42/actions'
            && $request->data() === ['type' => 'reboot']);
        Http::assertSent(fn (Request $request): bool => $request->data() === ['type' => 'power_on']);
        Http::assertSent(fn (Request $request): bool => $request->data() === ['type' => 'rename', 'name' => 'renamed']);
        Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && $request->url() === 'https://api.digitalocean.com/v2/droplets/42/actions/7');
    }
}
