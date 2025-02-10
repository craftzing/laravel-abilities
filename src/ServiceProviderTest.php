<?php

declare(strict_types=1);

namespace Craftzing\Laravel\Abilities;

use Craftzing\Laravel\Abilities\Testing\TestCase;
use Illuminate\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use PHPUnit\Framework\Attributes\Test;

final class ServiceProviderTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        // We should remove the package provider in order to test its
        // effect when registered with different configurations...
        return [];
    }

    #[Test]
    public function itShouldApplyAuthoriseUsingAbilitiesToGate(): void
    {
        $gate = $this->gate();
        $this->swap(GateContract::class, $gate);

        $this->app?->register(ServiceProvider::class);

        // @phpstan-ignore method.notFound
        $this->assertContainsEquals(new AuthoriseUsingAbilities($gate), $gate->beforeCallbacks());
    }

    #[Test]
    public function itShouldNotApplyAuthoriseUsingAbilitiesToGateWhenDisabled(): void
    {
        AuthoriseUsingAbilities::dontAutoApplyToGate();
        $gate = $this->gate();
        $this->swap(GateContract::class, $gate);

        $this->app?->register(ServiceProvider::class);

        // @phpstan-ignore method.notFound
        $this->assertEmpty($gate->beforeCallbacks());
    }

    private function gate(): Gate
    {
        // @phpstan-ignore argument.type
        return new class($this->app, fn () => null) extends Gate
        {
            /**
             * @return callable[]
             */
            public function beforeCallbacks(): array
            {
                return $this->beforeCallbacks;
            }
        };
    }
}
