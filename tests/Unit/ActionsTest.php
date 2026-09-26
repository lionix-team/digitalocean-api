<?php

declare(strict_types=1);

namespace Tests\Unit;

use Digitalocean\Facades\ActionsFacade;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;
use Tests\TestCase;

class ActionsTest extends TestCase
{
    public function test_wait_for_polls_until_completed(): void
    {
        Sleep::fake();
        Http::fakeSequence()
            ->push(['action' => ['id' => 9, 'status' => 'in-progress']])
            ->push(['action' => ['id' => 9, 'status' => 'in-progress']])
            ->push(['action' => ['id' => 9, 'status' => 'completed']]);

        $result = ActionsFacade::waitFor(9, timeout: 60, interval: 5);

        $this->assertSame('completed', $result['action']['status']);
        Http::assertSentCount(3);
        Http::assertSent(fn (Request $r): bool => $r->url() === 'https://api.digitalocean.com/v2/actions/9');
        Sleep::assertSleptTimes(2);
    }

    public function test_wait_for_gives_up_after_timeout(): void
    {
        Sleep::fake();
        Http::fake(['*' => Http::response(['action' => ['id' => 9, 'status' => 'in-progress']])]);

        $result = ActionsFacade::waitFor(9, timeout: 10, interval: 5);

        $this->assertSame('in-progress', $result['action']['status']);
        Http::assertSentCount(3);
    }

    public function test_snapshot_command_waits_and_only_then_drops_old_snapshots(): void
    {
        Sleep::fake();
        Http::fake([
            '*/droplets/42/snapshots*' => Http::response(['snapshots' => [['id' => 100, 'name' => 'old']]]),
            '*/droplets/42/actions' => Http::response(['action' => ['id' => 9, 'status' => 'in-progress']], 201),
            '*/actions/9' => Http::sequence()
                ->push(['action' => ['id' => 9, 'status' => 'in-progress']])
                ->push(['action' => ['id' => 9, 'status' => 'completed']]),
            '*/snapshots/100' => Http::response(null, 204),
        ]);

        $this->artisan('do:snapshot', ['--dropletId' => 42, '--dropOldSnapshots' => true, '--wait' => true])->assertSuccessful();

        $calls = Http::recorded()->map(fn (array $pair): string => $pair[0]->method().' '.parse_url($pair[0]->url(), PHP_URL_PATH))->all();
        $this->assertSame([
            'GET /v2/droplets/42/snapshots',
            'POST /v2/droplets/42/actions',
            'GET /v2/actions/9',
            'GET /v2/actions/9',
            'DELETE /v2/snapshots/100',
        ], $calls);
    }

    public function test_snapshot_command_keeps_old_snapshots_when_action_errors(): void
    {
        Sleep::fake();
        Http::fake([
            '*/droplets/42/snapshots*' => Http::response(['snapshots' => [['id' => 100, 'name' => 'old']]]),
            '*/droplets/42/actions' => Http::response(['action' => ['id' => 9, 'status' => 'in-progress']], 201),
            '*/actions/9' => Http::response(['action' => ['id' => 9, 'status' => 'errored']]),
        ]);

        $this->artisan('do:snapshot', ['--dropletId' => 42, '--dropOldSnapshots' => true, '--wait' => true])
            ->expectsOutputToContain('errored')
            ->assertFailed();

        Http::assertNotSent(fn (Request $r): bool => $r->method() === 'DELETE');
    }
}
