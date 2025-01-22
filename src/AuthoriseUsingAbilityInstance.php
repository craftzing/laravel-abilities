<?php

declare(strict_types=1);

namespace Craftzing\Laravel\Abilities;

use Craftzing\Laravel\Abilities\Contracts\Ability;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Authenticatable;

final readonly class AuthoriseUsingAbilityInstance
{
    public function __construct(
        private Gate $gate,
    ) {}

    /**
     * @param Authorizable|Authenticatable|mixed|null $user
     * @param mixed $ability
     * @return bool|null
     */
    public function __invoke(mixed $user, mixed $ability): ?bool
    {
        if (! $ability instanceof Ability) {
            return null;
        }

        // If the ability class is explicitly defined in the gate, we should handle
        // the ability using the resolver defined in the gate. This allows us
        // to define a resolver to fake ability results in tests...
        if ($this->gate->has($ability::class)) {
            return $this->gate->forUser($user)->allows($ability::class, $ability);
        }

        // When the ability class in not defined in the gate, we should resolve it on the fly...
        return $ability->granted($user);
    }
}