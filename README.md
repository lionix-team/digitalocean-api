# Digital Ocean API for Laravel Framework

![laravel-digitalocean](https://user-images.githubusercontent.com/12999189/208249278-b10946b5-2529-4f2f-b130-449d67ee8a34.png)

---

Digital Ocean API for Laravel Framework is a package created by Arayik Smbatyan ([@arayiksmbatyan](https://github.com/arayiksmbatyan)) from **Lionix** to make it easier to use Digital Ocean API in Laravel Framework.

The package is not using any external libraries like DO PHP SDK, it uses general DO API, therefore it is very easy extendable.

## Installation

You can install the package via composer:

```bash
composer require lionix/digitalocean
```

## Publishing the config file

```bash
php artisan vendor:publish --provider="Lionix\DigitalOcean\DigitalOceanServiceProvider" --tag="config"
```

## API KEY

Open your Digitalocean Account and go to API section. [Generate a new Personal Access Token](https://cloud.digitalocean.com/account/api/tokens/new?i=c1d240) with `write` access and add to your `.env` file.

```apacheconf
DO_API_KEY=your_api_key
```

## Available Services

- [Droplets](#droplets)
- [Droplet Actions](https://docs.lionix.io/laravel-digitalocean-api#droplet-actions)
- [Domains](https://docs.lionix.io/laravel-digitalocean-api#domains)
- [Snapshots](https://docs.lionix.io/laravel-digitalocean-api#snapshots)
- [Global Service](https://docs.lionix.io/laravel-digitalocean-api#global-service)
- [DO Snapshot Command](https://docs.lionix.io/laravel-digitalocean-api#do-snapshot-command)

All the services can be used 
by injecting the service into 
your controller, 
by using the `Digitalocean` facade or by using the service facade (e.g. `Droplets`).


## Droplets

### Using via Service
```php
<?php

namespace App\Http\Controllers;

use Digitalocean\Services\DropletsService;

class DigitalOceanController extends Controller
{
    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function droplets(DropletsService $dropletsService): \Illuminate\Http\JsonResponse
    {
        $droplets = $dropletsService->list();

        return response()->json($droplets);
    }
}
```

### Using via Facade

```php
Droplets::list();
```

### Using via Digitalocean Facade

```php
Digitalocean::droplets()->list();
```

### Available Methods

- `list()`
- `store()`
- `show()`
- `destroy()`

---

### Read full documentation in our [Docs](https://docs.lionix.io/laravel-digitalocean-api)

