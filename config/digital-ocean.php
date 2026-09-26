<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Personal Access Token
    |--------------------------------------------------------------------------
    |
    | Generate a token with read/write scope at
    | https://cloud.digitalocean.com/account/api/tokens
    |
    */

    'token' => env('DO_APP_TOKEN', ''),

    'base_url' => env('DO_BASE_URL', 'https://api.digitalocean.com/v2/'),

    /*
    |--------------------------------------------------------------------------
    | Default Droplet
    |--------------------------------------------------------------------------
    |
    | Used by the `do:snapshot` command when no --dropletId option is given.
    |
    */

    'droplet_id' => env('DO_DROPLET_ID'),

    /*
    |--------------------------------------------------------------------------
    | Default CDN Endpoint
    |--------------------------------------------------------------------------
    |
    | Used by the `do:cdn-purge` command when no endpoint ID is given.
    |
    */

    'cdn_endpoint_id' => env('DO_CDN_ENDPOINT_ID'),

    /*
    |--------------------------------------------------------------------------
    | HTTP Options
    |--------------------------------------------------------------------------
    */

    'timeout' => (int) env('DO_TIMEOUT', 30),

    'retry' => [
        'times' => (int) env('DO_RETRY_TIMES', 0),
        'sleep' => (int) env('DO_RETRY_SLEEP', 100),
    ],

    'endpoints' => [
        'account' => 'account',
        'actions' => 'actions',
        'balance' => 'customers/my/balance',
        'cdn' => 'cdn/endpoints',
        'domains' => 'domains',
        'domain_records' => 'domains/:domain/records',
        'droplets' => [
            'index' => 'droplets',
            'snapshots' => 'droplets/:dropletId/snapshots',
            'actions' => 'droplets/:dropletId/actions',
        ],
        'firewalls' => 'firewalls',
        'images' => 'images',
        'regions' => 'regions',
        'reserved_ips' => 'reserved_ips',
        'sizes' => 'sizes',
        'snapshots' => 'snapshots',
        'ssh_keys' => 'account/keys',
    ],

];
