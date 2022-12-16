<?php

namespace Tests\Feature\Droplets;

use Tests\TestCase;
use Digitalocean\Services\DropletsService;

class TestDropletService extends TestCase
{
    private $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(DropletsService::class);
    }

    public function testIndex()
    {
        $result = $this->service->list();
        file_put_contents("tests/logs/DropletServiceLog.log", json_encode($result).PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}
