<?php

declare(strict_types=1);

namespace Craftzing\Laravel\Tests\Abilities;

use Craftzing\Laravel\Abilities\Contracts\Ability;
use Craftzing\Laravel\Tests\Abilities\Doubles\SpyCallable;
use Exception;
use Illuminate\Contracts\Auth\Access\Gate;
use PHPUnit\Framework\Assert;

final class SpyAbility implements Ability
{
    private static bool $authorise = true;

    public SpyCallable $granted;
    private array $args;

    public function __construct(mixed ...$args)
    {
        $this->granted = new SpyCallable(self::$authorise);
        $this->args = $args;
    }

    public static function authorise(bool $authorise = true): self
    {
        self::$authorise = $authorise;

        return new self();
    }

    public static function define(Gate $gate, bool $granted): self
    {
        $instance = self::authorise($granted);

        $gate->define($instance::class, $instance->granted(...));

        return $instance;
    }

    /**
     * @throws Exception
     */
    public function granted(mixed $user): bool
    {
        return $this->granted->__invoke($user);
    }

    public function assertConstructedWith(mixed ...$args): void
    {
        Assert::assertEquals($args, $this->args);
    }

    public function assertConstructedWithoutArgs(): void
    {
        Assert::assertEmpty($this->args);
    }
}