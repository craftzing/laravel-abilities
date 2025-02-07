<?php

declare(strict_types=1);

namespace Craftzing\Laravel\Abilities;

use Closure;
use Craftzing\Laravel\Abilities\Testing\TestCase;
use Illuminate\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\Test;

final class ServiceProviderTest extends TestCase
{
    #[Test]
    public function itShouldRegisterBeforeCallbacksOnGate(): void
    {
        $gate = $this->make(GateContract::class);

        $expectedBeforeCallbacks = [
            new AuthoriseUsingAbilityInstance($gate),
        ];

        Closure::bind(function (Gate $gate, array $expectedBeforeCallbacks): void {
            Assert::assertEquals($expectedBeforeCallbacks, $gate->beforeCallbacks);
        }, null, Gate::class)($gate, $expectedBeforeCallbacks); // @phpstan-ignore-line argument.type
    }
}
