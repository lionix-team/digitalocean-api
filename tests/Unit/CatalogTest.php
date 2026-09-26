<?php

declare(strict_types=1);

namespace Tests\Unit;

use Digitalocean\Facades\DigitaloceanFacade;
use Digitalocean\Facades\ImagesFacade;
use Digitalocean\Facades\RegionsFacade;
use Digitalocean\Facades\SizesFacade;
use Digitalocean\Facades\SshKeysFacade;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    private const API = 'https://api.digitalocean.com/v2/';

    public function test_regions_sizes_and_images(): void
    {
        Http::fake(['*' => Http::response([])]);

        RegionsFacade::list();
        SizesFacade::list();
        ImagesFacade::list('distribution', private: false);
        ImagesFacade::show('ubuntu-24-04-x64');

        Http::assertSent(fn (Request $r): bool => $r->url() === self::API.'regions?per_page=200&page=1');
        Http::assertSent(fn (Request $r): bool => $r->url() === self::API.'sizes?per_page=200&page=1');
        Http::assertSent(fn (Request $r): bool => $r->url() === self::API.'images?type=distribution&private=false&per_page=20&page=1');
        Http::assertSent(fn (Request $r): bool => $r->url() === self::API.'images/ubuntu-24-04-x64');
    }

    public function test_ssh_keys(): void
    {
        Http::fake(['*' => Http::response(['ssh_key' => ['id' => 1]], 201)]);

        SshKeysFacade::store('laptop', 'ssh-ed25519 AAAA...');
        SshKeysFacade::update('3b:16:bf', 'renamed');
        SshKeysFacade::destroy(1);

        Http::assertSent(fn (Request $r): bool => $r->method() === 'POST' && $r->url() === self::API.'account/keys'
            && $r->data() === ['name' => 'laptop', 'public_key' => 'ssh-ed25519 AAAA...']);
        Http::assertSent(fn (Request $r): bool => $r->method() === 'PUT' && $r->url() === self::API.'account/keys/3b:16:bf');
        Http::assertSent(fn (Request $r): bool => $r->method() === 'DELETE' && $r->url() === self::API.'account/keys/1');
    }

    public function test_ssh_key_store_validates(): void
    {
        Http::fake();

        $this->expectException(ValidationException::class);

        SshKeysFacade::store('laptop', '');
    }

    public function test_account_and_balance(): void
    {
        Http::fake(['*' => Http::response(['account' => []])]);

        DigitaloceanFacade::account()->show();
        DigitaloceanFacade::account()->balance();

        Http::assertSent(fn (Request $r): bool => $r->url() === self::API.'account');
        Http::assertSent(fn (Request $r): bool => $r->url() === self::API.'customers/my/balance');
    }
}
