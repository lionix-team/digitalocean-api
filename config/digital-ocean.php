<?php

return [
    'token' => env('TOKEN', ''),
    'api-urls' => [
        'domains' => 'https://api.digitalocean.com/v2/domains',
        'droplets' => 'https://api.digitalocean.com/v2/droplets'
    ],
];