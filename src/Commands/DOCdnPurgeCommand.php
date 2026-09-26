<?php

declare(strict_types=1);

namespace Digitalocean\Commands;

use Digitalocean\Services\CdnService;
use Illuminate\Console\Command;

class DOCdnPurgeCommand extends Command
{
    protected $signature = 'do:cdn-purge
                            {endpoint? : The CDN endpoint ID (defaults to the DO_CDN_ENDPOINT_ID env value)}
                            {--file=* : Path to purge, wildcards allowed (repeatable, defaults to everything)}';

    protected $description = 'Purge cached files from a DigitalOcean CDN endpoint';

    public function handle(CdnService $cdn): int
    {
        $endpoint = $this->argument('endpoint') ?? config('digital-ocean.cdn_endpoint_id');

        if (! $endpoint) {
            $this->components->error('A CDN endpoint ID is required (argument or DO_CDN_ENDPOINT_ID).');

            return self::FAILURE;
        }

        $files = $this->option('file') ?: ['*'];

        $response = $cdn->purge((string) $endpoint, $files);
        $status = $response['status_code'];

        if ($status < 200 || $status >= 300) {
            $this->components->error('CDN purge failed: '.($response['message'] ?? 'unknown error'));

            return self::FAILURE;
        }

        $this->components->info('Purged '.implode(', ', $files).' from CDN endpoint '.$endpoint.'.');

        return self::SUCCESS;
    }
}
