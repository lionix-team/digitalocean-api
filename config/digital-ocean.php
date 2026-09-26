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
    | HTTP Options
    |--------------------------------------------------------------------------
    */

    'timeout' => (int) env('DO_TIMEOUT', 30),

    'retry' => [
        'times' => (int) env('DO_RETRY_TIMES', 0),
        'sleep' => (int) env('DO_RETRY_SLEEP', 100),
    ],

    'endpoints' => [
        'domains' => 'domains',
        'droplets' => [
            'index' => 'droplets',
            'snapshots' => 'droplets/:dropletId/snapshots',
            'actions' => 'droplets/:dropletId/actions',
        ],
        'snapshots' => 'snapshots',
    ],

];
