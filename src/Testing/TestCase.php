<?php

declare(strict_types=1);

namespace Craftzing\Laravel\Abilities\Testing;

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

    /**
     * @template TReturn of mixed
     * @param class-string<TReturn> $fqcn
     * @return TReturn
     */
    protected function make(string $fqcn): mixed
    {
        return $this->app?->make($fqcn);
    }
}
