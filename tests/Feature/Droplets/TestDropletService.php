<?php

namespace Tests\Feature\Droplets;

use Tests\TestCase;
use DigitaloceanApi\Services\DropletService;

class TestDropletService extends TestCase
{
    private $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(DropletService::class);
    }

    public function testIndex()
    {
        $result = $this->service->index();
        file_put_contents("tests/logs/DropletServiceLog.log", json_encode($result).PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}