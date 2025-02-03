<?php

declare(strict_types=1);

namespace Craftzing\Laravel\Abilities;

use Closure;
use Craftzing\Laravel\Tests\Abilities\TestCase;
use Illuminate\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use PHPUnit\Framework\Attributes\Test;

final class ServiceProviderTest extends TestCase
{
    #[Test]
    public function itShouldRegisterBeforeCallbacksOnGate(): void
    {
        $gate = $this->app[GateContract::class];

        $expectedBeforeCallbacks = [
            new AuthoriseUsingAbilityInstance($gate),
        ];

        Closure::bind(function (Gate $gate, array $expectedBeforeCallbacks): void {
            $this->assertEquals($expectedBeforeCallbacks, $gate->beforeCallbacks);
        }, $this, Gate::class)($gate, $expectedBeforeCallbacks);
    }
}
