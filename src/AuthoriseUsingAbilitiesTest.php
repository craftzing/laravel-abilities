<?php

declare(strict_types=1);

namespace Craftzing\Laravel\Abilities;

use Craftzing\Laravel\Abilities\Testing\Doubles\FakeAbility;
use Illuminate\Auth\Access\Gate;
use Illuminate\Auth\GenericUser;
use Illuminate\Contracts\Container\Container;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class AuthoriseUsingAbilitiesTest extends TestCase
{
    #[Test]
    public function itShouldIgnoreAbilitiesThatAreNotAnInstance(): void
    {
        $user = new GenericUser(['id' => 1]);
        $gate = $this->fakeGate();
        $instance = new AuthoriseUsingAbilities($gate);

        $result = $instance($user, 'non-instance-ability');

        $this->assertNull($result);
    }

    /**
     * @return iterable<array<bool>>
     */
    public static function authorize(): iterable
    {
        yield 'Allowed' => [true];
        yield 'Denied' => [false];
    }

    #[Test]
    #[DataProvider('authorize')]
    public function itCanAuthorizeAbilitiesNotDefinedInGate(bool $allowed): void
    {
        $gate = $this->fakeGate();
        $user = new GenericUser(['id' => 1]);
        $ability = FakeAbility::authorizeIf($allowed, $user);
        $instance = new AuthoriseUsingAbilities($gate);

        $result = $instance($user, $ability);

        $this->assertSame($allowed, $result);
    }

    #[Test]
    #[DataProvider('authorize')]
    public function itCanAuthorizeAbilitiesExplicitlyDefinedInGate(bool $allowed): void
    {
        $gate = $this->fakeGate();
        $user = new GenericUser(['id' => 1]);
        $ability = FakeAbility::authorizeIf($allowed, $user);
        $gate = $gate->define($ability::class, $ability->granted(...));
        $instance = new AuthoriseUsingAbilities($gate);

        $result = $instance($user, $ability);

        $this->assertSame($allowed, $result);
    }

    private function fakeGate(): Gate
    {
        return new Gate($this->createMock(Container::class), fn () => null);
    }
}
