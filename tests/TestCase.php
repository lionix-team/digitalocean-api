<?php

declare(strict_types=1);

namespace Tests;

use Digitalocean\DigitaloceanServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            DigitaloceanServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('digital-ocean.token', 'test-token');
    }
}
