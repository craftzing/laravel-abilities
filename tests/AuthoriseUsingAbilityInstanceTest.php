<?php

declare(strict_types=1);

namespace Craftzing\Laravel\Tests\Abilities;

use Craftzing\Laravel\Abilities\AuthoriseUsingAbilityInstance;
use Craftzing\Laravel\Tests\Abilities\Doubles\SpyCallable;
use Illuminate\Auth\Access\Gate;
use Illuminate\Auth\GenericUser;
use Illuminate\Container\Container;
use PHPUnit\Framework\Attributes\Test;

use function fake;

final class AuthoriseUsingAbilityInstanceTest extends TestCase
{

    #[Test]
    public function itCanResolveAbilitiesWithoutHavingToDefineThem(): void
    {
        $user = new GenericUser(['id' => 1]);
        $allowed = fake()->boolean();
        $spyAbility = SpyAbility::authorise($allowed);
        $gate = $this->getBasicGate();

        $result = (new AuthoriseUsingAbilityInstance($gate))($user, $spyAbility);

        $this->assertSame($allowed, $result);
        $spyAbility->granted->assertWasCalledOnceWithArguments($user);
    }

    #[Test]
    public function itCanResolveAbilitiesThatAreExplicitlyDefined(): void
    {
        $user = new GenericUser(['id' => 1]);
        $allowed = fake()->boolean();
        $spyAbility = SpyAbility::authorise($allowed);
        $spyResolver = new SpyCallable($allowed);
        $gate = $this->getBasicGate();
        $gate = $gate->define($spyAbility::class, $spyResolver(...));

        $result = (new AuthoriseUsingAbilityInstance($gate))($user, $spyAbility);

        $this->assertSame($allowed, $result);
        $spyAbility->granted->assertWasNotCalled();
        $spyResolver->assertWasCalledOnceWithArguments($user, $spyAbility);
    }

    protected function getBasicGate(bool $isAdmin = false): Gate
    {
        return new Gate(
            new Container(),
            fn (): object => (object) ['id' => 1, 'isAdmin' => $isAdmin],
        );
    }
}