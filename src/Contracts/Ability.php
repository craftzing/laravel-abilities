<?php

declare(strict_types=1);

namespace Craftzing\Laravel\Abilities\Contracts;

use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable;

interface Ability
{
    /**
     * @param Authenticatable|Authorizable|mixed $user
     * @return bool
     */
    public function granted(mixed $user): bool;
}