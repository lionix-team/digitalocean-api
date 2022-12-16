<?php

namespace Tests\Feature\Domains;

use Tests\TestCase;
use Digitalocean\Services\DomainService;

class TestDomainService extends TestCase
{

    private $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(DomainService::class);
    }

    public function testIndex()
    {
        $result = $this->service->list();
        file_put_contents("tests/logs/DomainServiceLogs.log", json_encode($result).PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    public function testStore()
    {
        $result = $this->service->store(['name' => 'digital-ocean-package.io']);
        file_put_contents("tests/logs/DomainServiceLogs.log", json_encode($result).PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    public function testShow()
    {
        $result = $this->service->show('digital-ocean-package.io');
        file_put_contents("tests/logs/DomainServiceLogs.log", json_encode($result).PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    public function testDestroy()
    {
        $result = $this->service->destroy('digital-ocean-package.io');
        file_put_contents("tests/logs/DomainServiceLogs.log", json_encode($result).PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}
