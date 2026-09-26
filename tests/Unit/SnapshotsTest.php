<?php

declare(strict_types=1);

namespace Tests\Unit;

use Digitalocean\Facades\SnapshotsFacade;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SnapshotsTest extends TestCase
{
    public function test_make_snapshot(): void
    {
        Carbon::setTestNow('2026-01-02 03:04:05');
        Http::fake(['*' => Http::response(['action' => ['id' => 1]], 201)]);

        SnapshotsFacade::make(42, 'backup');

        Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
            && $request->url() === 'https://api.digitalocean.com/v2/droplets/42/actions'
            && $request->data() === ['type' => 'snapshot', 'name' => 'backup-2026-01-02 03:04:05']);
    }

    public function test_list_all_and_destroy(): void
    {
        Http::fake(['*' => Http::response(['snapshots' => []])]);

        SnapshotsFacade::list(42);
        SnapshotsFacade::all('droplet');
        SnapshotsFacade::destroy('123');

        Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api.digitalocean.com/v2/droplets/42/snapshots?per_page=20&page=1');
        Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api.digitalocean.com/v2/snapshots?resource_type=droplet&per_page=20&page=1');
        Http::assertSent(fn (Request $request): bool => $request->method() === 'DELETE'
            && $request->url() === 'https://api.digitalocean.com/v2/snapshots/123');
    }

    public function test_command_creates_snapshot_then_drops_old_ones(): void
    {
        config(['digital-ocean.droplet_id' => 42]);

        Http::fake([
            'api.digitalocean.com/v2/droplets/42/snapshots*' => Http::response(['snapshots' => [['id' => 100, 'name' => 'old']]]),
            'api.digitalocean.com/v2/droplets/42/actions' => Http::response(['action' => ['id' => 1]], 201),
            'api.digitalocean.com/v2/snapshots/100' => Http::response(null, 204),
        ]);

        $this->artisan('do:snapshot', ['--dropOldSnapshots' => true])->assertSuccessful();

        $methods = Http::recorded()->map(fn (array $pair): string => $pair[0]->method())->all();
        $this->assertSame(['GET', 'POST', 'DELETE'], $methods);
    }

    public function test_command_keeps_old_snapshots_when_creation_fails(): void
    {
        Http::fake([
            '*/snapshots*' => Http::response(['snapshots' => [['id' => 100, 'name' => 'old']]]),
            '*/actions' => Http::response(['id' => 'unprocessable_entity', 'message' => 'Droplet is busy'], 422),
        ]);

        $this->artisan('do:snapshot', ['--dropletId' => 42, '--dropOldSnapshots' => true])
            ->expectsOutputToContain('Droplet is busy')
            ->assertFailed();

        Http::assertNotSent(fn (Request $request): bool => $request->method() === 'DELETE');
    }

    public function test_command_asks_for_droplet_id(): void
    {
        Http::fake(['*' => Http::response(['action' => []], 201)]);

        $this->artisan('do:snapshot')
            ->expectsQuestion('What is your droplet id?', '42')
            ->assertSuccessful();
    }
}
