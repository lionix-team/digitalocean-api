<?php

declare(strict_types=1);

namespace Digitalocean\Commands;

use Digitalocean\Services\ActionsService;
use Digitalocean\Services\SnapshotsService;
use Illuminate\Console\Command;

class DOSnapshotCommand extends Command
{
    protected $signature = 'do:snapshot
                            {--dropletId= : The droplet ID (defaults to the DO_DROPLET_ID env value)}
                            {--name= : Snapshot name prefix (defaults to the droplet ID)}
                            {--dropOldSnapshots : Delete the droplet\'s existing snapshots after the new one is requested (or completed with --wait)}
                            {--wait : Wait until the snapshot has finished}
                            {--timeout=3600 : Maximum seconds to wait with --wait}';

    protected $description = 'Create a DigitalOcean droplet snapshot';

    public function handle(SnapshotsService $snapshots, ActionsService $actions): int
    {
        $dropletId = $this->option('dropletId')
            ?? config('digital-ocean.droplet_id')
            ?? config('digital-ocean.dropletId') // 1.x config key
            ?? $this->ask('What is your droplet id?');

        if (! is_numeric($dropletId)) {
            $this->components->error('A numeric droplet ID is required.');

            return self::FAILURE;
        }

        $dropletId = (int) $dropletId;

        // Collect existing snapshots up-front so the new one is never removed.
        $oldSnapshots = [];

        if ($this->option('dropOldSnapshots')) {
            $existing = $snapshots->list($dropletId, perPage: 200);

            if (! $this->successful($existing)) {
                $this->components->error('Could not list snapshots: '.($existing['message'] ?? 'unknown error'));

                return self::FAILURE;
            }

            $oldSnapshots = $existing['snapshots'] ?? [];
        }

        $response = $snapshots->make($dropletId, $this->option('name'));

        if (! $this->successful($response)) {
            $this->components->error('Snapshot failed: '.($response['message'] ?? 'unknown error'));

            return self::FAILURE;
        }

        $this->components->info("Snapshot for droplet {$dropletId} requested successfully.");

        if ($this->option('wait') && isset($response['action']['id'])) {
            $this->components->info('Waiting for the snapshot to finish...');

            $action = $actions->waitFor((int) $response['action']['id'], (int) $this->option('timeout'));

            if (! ActionsService::completed($action)) {
                $status = $action['action']['status'] ?? ($action['message'] ?? 'unknown');
                $this->components->error("Snapshot did not complete (status: {$status}). Old snapshots were kept.");

                return self::FAILURE;
            }

            $this->components->info('Snapshot completed.');
        }

        foreach ($oldSnapshots as $snapshot) {
            $this->components->task(
                "Deleting snapshot {$snapshot['name']}",
                fn (): bool => $this->successful($snapshots->destroy($snapshot['id'])),
            );
        }

        return self::SUCCESS;
    }

    /**
     * @param  array<string, mixed>  $response
     */
    private function successful(array $response): bool
    {
        $status = $response['status_code'] ?? 0;

        return $status >= 200 && $status < 300;
    }
}
