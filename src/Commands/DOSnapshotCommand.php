<?php

namespace Digitalocean\Commands;

use Digitalocean\Services\DropletActionsService;
use Digitalocean\Services\SnapshotsService;
use Illuminate\Console\Command;

class DOSnapshotCommand extends Command
{
    protected $signature = 'do:snapshot {--dropletId=} {--name=} {--dropOldSnapshots}';

    protected $description = 'Create Digital Ocean Snapshot';

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function handle(SnapshotsService $snapshotService): int
    {
        $dropletId = $this->option('dropletId') ?? config('digital-ocean.droplet_id');

        if (!$dropletId) {
            $dropletId = $this->ask('What is your droplet id?');
        }

        if ($this->option('dropOldSnapshots')) {
            $snapshots = $snapshotService->list($dropletId);

            collect($snapshots['snapshots'])->each(fn($snapshot) => $snapshotService->destroy($snapshot['id']));
        }

        $response = $snapshotService->make((int)$dropletId, $this->option('name'));

        if ($response['status_code'] === 200 || $response['status_code'] === 201) {
            $this->info('Snapshot for droplet ' . $dropletId . ' created successfully');
        } else {
            $this->info('Something went wrong');
        }

        return 0;
    }
}
