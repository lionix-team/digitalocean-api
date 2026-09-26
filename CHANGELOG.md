# Changelog

All notable changes to this project are documented in this file.
The project follows [Semantic Versioning](https://semver.org/).

## [2.0.0] - 2026-09-26

A modernization release for current PHP and Laravel versions.

### Requirements

- PHP **8.2 – 8.5** (was `^8.0`).
- Laravel **12 and 13** (Laravel 13 requires PHP 8.3+). Laravel 11 is end-of-life and every 11.x release has open
  security advisories, so it is not supported.

### Added

- `DropletActionType` enum covering every droplet action type.
- Droplet action helpers: `powerOn()`, `powerOff()`, `powerCycle()`, `shutdown()`, `reboot()`, `rename()`,
  `resize()`, `snapshot()`, plus `list()` and `show()` for action history.
- `Snapshots::all()` to list every snapshot on the account (optionally filtered by `droplet` / `volume`).
- Pagination arguments on `Domains::list()`, `Snapshots::list()` and a `tagName` filter on `Droplets::list()`.
- Support for multi-droplet creation (`names`) in `Droplets::store()`.
- `DigitaloceanApi::request()` returns a pre-configured `PendingRequest` for endpoints without a wrapper.
- `timeout` and `retry` config options (`DO_TIMEOUT`, `DO_RETRY_TIMES`, `DO_RETRY_SLEEP`).
- Typed return values, `declare(strict_types=1)` and `@method` facade annotations for IDE autocompletion.
- An offline test suite built on `Http::fake()`, and a GitHub Actions matrix for PHP 8.2–8.5 × Laravel 12–13.

### Changed

- HTTP calls go through Laravel's HTTP client instead of a raw Guzzle client, so `Http::fake()` works in your tests.
- `GET`/`DELETE` parameters are sent as query strings. Previously they were sent as a JSON body and ignored by the
  API, so pagination did not work.
- `store()` methods throw `Illuminate\Validation\ValidationException` instead of returning a `MessageBag`.
- All service methods return `array` (decoded JSON + `status_code`).
- The service provider is now `Digitalocean\DigitaloceanServiceProvider`.
- The config publish tag is `digital-ocean-config`.
- The config key for the default droplet is `droplet_id` (the `do:snapshot` command still reads the old
  `dropletId` key as a fallback).
- `do:snapshot` now creates the new snapshot **before** deleting old ones, never deletes anything if creation
  fails, and returns a non-zero exit code on failure.

### Fixed

- The `DropletActions` facade alias pointed to a non-existent class.
- `do:snapshot` ignored `DO_DROPLET_ID` because of a config key mismatch.
- Droplet creation rejected valid image slugs such as `ubuntu-24-04-x64`.
- `DELETE` requests raised an undefined-variable warning, and network errors caused a fatal error on
  `null` responses.
- Removed PHP 8.2+/8.4+ deprecations (`${var}` string interpolation, implicitly nullable parameters).
- The README documented the wrong env variable (`DO_API_KEY`) and provider class.

### Removed

- The live-API test suite and `tests/logs`.

### Deprecated

- `Digitalocean\Providers\ConfigServiceProvider`. It still works as an alias of the new provider and will be
  removed in 3.0.

## Upgrading from 1.x

1. Make sure your application runs on PHP 8.2+ and Laravel 12+, then run
   `composer require lionix/digitalocean:^2.0`.
2. If you published `config/digital-ocean.php`, rename `dropletId` to `droplet_id`, or re-publish it with
   `php artisan vendor:publish --tag=digital-ocean-config --force`.
3. If you registered `Digitalocean\Providers\ConfigServiceProvider` manually, replace it with
   `Digitalocean\DigitaloceanServiceProvider` (or rely on package discovery).
4. If you checked `store()` results for a `MessageBag`, catch `Illuminate\Validation\ValidationException` instead.
5. If you caught `GuzzleHttp\Exception\GuzzleException` or `JsonException`, catch
   `Illuminate\Http\Client\ConnectionException` instead. API errors are still returned as arrays with a
   `status_code`.

## [1.0.0]

- Initial release.
