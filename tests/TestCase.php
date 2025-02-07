<?php

declare(strict_types=1);

namespace Craftzing\Laravel\Tests\Abilities;

use Craftzing\Laravel\Abilities\ServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getPackageProviders($app): array
    {
        return [
            ServiceProvider::class,
        ];
    }
}
