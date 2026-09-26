# DigitalOcean API for Laravel

![laravel-digitalocean](https://user-images.githubusercontent.com/12999189/208249278-b10946b5-2529-4f2f-b130-449d67ee8a34.png)

[![tests](https://github.com/lionix-team/digitalocean-api/actions/workflows/tests.yml/badge.svg)](https://github.com/lionix-team/digitalocean-api/actions/workflows/tests.yml)
[![Latest Version](https://img.shields.io/packagist/v/lionix/digitalocean.svg)](https://packagist.org/packages/lionix/digitalocean)
[![License](https://img.shields.io/packagist/l/lionix/digitalocean.svg)](LICENSE)

A lightweight [DigitalOcean API v2](https://docs.digitalocean.com/reference/api/) client for Laravel, created by
Arayik Smbatyan ([@arayiksmbatyan](https://github.com/arayiksmbatyan)) at **Lionix**.

It talks to the REST API directly through Laravel's HTTP client — no external SDK — so it is easy to extend
and fully testable with `Http::fake()`.

## Requirements

| Package | PHP         | Laravel      |
|---------|-------------|--------------|
| 2.x     | 8.2 – 8.5   | 12, 13       |
| 1.x     | 8.0+        | 8 – 10       |

> Laravel 13 itself requires PHP 8.3+.

## Installation

```bash
composer require lionix/digitalocean
```

The service provider and facades are registered automatically via package discovery.

Publish the config file (optional):

```bash
php artisan vendor:publish --tag=digital-ocean-config
```

## Configuration

[Generate a Personal Access Token](https://cloud.digitalocean.com/account/api/tokens) with **write** scope and
add it to your `.env` file:

```dotenv
DO_APP_TOKEN=your-token

# Optional
DO_DROPLET_ID=123456789   # default droplet for the do:snapshot command
DO_TIMEOUT=30             # request timeout in seconds
DO_RETRY_TIMES=0          # retries on connection errors, 429 and 5xx responses
DO_RETRY_SLEEP=100        # milliseconds between retries
```

## Usage

Every service can be used in three ways:

```php
use Digitalocean\Services\DropletsService;

// 1. Dependency injection
public function index(DropletsService $droplets)
{
    return $droplets->list();
}

// 2. Service facade
Droplets::list();

// 3. Global facade
Digitalocean::droplets()->list();
```

### Responses

Every method returns the decoded JSON response as an array with the HTTP status code added under `status_code`.
API errors are returned the same way, so you can check them without catching exceptions:

```php
$response = Droplets::show(123);

if ($response['status_code'] !== 200) {
    // e.g. ['id' => 'not_found', 'message' => 'The resource you requested could not be found.', 'status_code' => 404]
    logger()->error($response['message']);
}
```

`store()` methods validate their input first and throw `Illuminate\Validation\ValidationException` on invalid
data. Network failures throw `Illuminate\Http\Client\ConnectionException`.

### Droplets

```php
Droplets::list(perPage: 20, page: 1, tagName: 'web');

Droplets::store([
    'name'   => 'web-1',          // or 'names' => ['web-1', 'web-2']
    'region' => 'nyc3',
    'size'   => 's-1vcpu-1gb',
    'image'  => 'ubuntu-24-04-x64',
    'ssh_keys' => [12345],
    'tags'   => ['web'],
]);

Droplets::show(123);
Droplets::destroy(123);
```

### Droplet Actions

```php
use Digitalocean\Enums\DropletActionType;

DropletActions::powerOn(123);
DropletActions::powerOff(123);
DropletActions::powerCycle(123);
DropletActions::shutdown(123);
DropletActions::reboot(123);
DropletActions::rename(123, 'new-name');
DropletActions::resize(123, 's-2vcpu-4gb', disk: true);
DropletActions::snapshot(123, 'before-upgrade');

// Any other action type
DropletActions::initiate(123, DropletActionType::EnableBackups);
DropletActions::initiate(123, 'rebuild', ['image' => 'ubuntu-24-04-x64']);

DropletActions::list(123);
DropletActions::show(123, $actionId);
```

### Domains

```php
Domains::list();
Domains::store(['name' => 'example.com', 'ip_address' => '1.2.3.4']);
Domains::show('example.com');
Domains::destroy('example.com');
```

### Snapshots

```php
Snapshots::list(123);               // snapshots of a droplet
Snapshots::all('droplet');          // all account snapshots, optionally 'droplet' or 'volume'
Snapshots::make(123, 'nightly');    // named "nightly-2026-01-01 00:00:00"
Snapshots::show($snapshotId);
Snapshots::destroy($snapshotId);
```

### Any other endpoint

The global service can call any endpoint that does not have a dedicated wrapper yet:

```php
Digitalocean::send('GET', 'account');
Digitalocean::send('POST', 'tags', ['name' => 'production']);
```

For full control you can grab a pre-configured `PendingRequest` (base URL, token, timeout and retries already set):

```php
app(\Digitalocean\Services\DigitaloceanApi::class)->request()->get('sizes')->json();
```

## Snapshot command

```bash
php artisan do:snapshot --dropletId=123 --name=nightly --dropOldSnapshots
```

| Option               | Description                                                                          |
|----------------------|--------------------------------------------------------------------------------------|
| `--dropletId`        | Droplet ID. Falls back to `DO_DROPLET_ID`, then asks interactively.                  |
| `--name`             | Snapshot name prefix (defaults to the droplet ID). The current date-time is appended. |
| `--dropOldSnapshots` | Delete the droplet's existing snapshots once the new snapshot is requested.          |

Old snapshots are only removed if the new snapshot request succeeds. The command exits with a non-zero code on
failure, so it is safe to schedule:

```php
// routes/console.php
use Illuminate\Support\Facades\Schedule;

Schedule::command('do:snapshot --dropOldSnapshots')->dailyAt('03:00');
```

## Testing

Because the package uses Laravel's HTTP client, you can fake DigitalOcean in your own tests:

```php
Http::fake([
    'api.digitalocean.com/v2/droplets*' => Http::response(['droplets' => []]),
]);
```

Run the package test suite:

```bash
composer test
```

## Upgrading from 1.x

See [CHANGELOG.md](CHANGELOG.md#upgrading-from-1x).

## License

The MIT License (MIT).
