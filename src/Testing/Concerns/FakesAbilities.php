<?php

declare(strict_types=1);

namespace Craftzing\Laravel\Abilities\Testing\Concerns;

use Illuminate\Support\Facades\Gate;

trait FakesAbilities
{
    public function allow(string $ability): void
    {
        Gate::define($ability, fn (): bool => true);
    }

    public function deny(string $ability): void
    {
        Gate::define($ability, fn (): bool => false);
    }
}