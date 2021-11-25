<?php

namespace Tests\Feature\Domains;

use Tests\TestCase;
use DigitaloceanApi\Services\DomainService;
use function PHPUnit\Framework\assertTrue;

class TestDomainService extends TestCase
{

    private $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(DomainService::class);
    }

    public function testGago()
    {
        $this->service->index();
    }
}