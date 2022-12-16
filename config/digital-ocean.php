<?php

return [
    'token' => env('DO_APP_TOKEN', ''),
    'base_url' => 'https://api.digitalocean.com/v2/',
    'dropletId' => env('DO_DROPLET_ID'),

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
