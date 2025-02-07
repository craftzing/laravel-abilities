<?php

declare(strict_types=1);

namespace Craftzing\Laravel\Abilities\Testing\Doubles;

use Craftzing\Laravel\Abilities\Contracts\Ability;
use Illuminate\Contracts\Auth\Authenticatable;

use function in_array;

final class FakeAbility implements Ability
{
    /**
     * @var \Illuminate\Contracts\Auth\Authenticatable[]
     */
    private array $allowedUsers;

    public function __construct(Authenticatable ...$allowedUsers)
    {
        $this->allowedUsers = $allowedUsers;
    }

    public static function authorizeIf(bool $allowed, Authenticatable ...$allowedUsers): self
    {
        if ($allowed === false) {
            return new self();
        }

        return new self(...$allowedUsers);
    }

    public function granted(mixed $user): bool
    {
        return in_array($user, $this->allowedUsers, true);
    }
}
