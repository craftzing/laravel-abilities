<?php

declare(strict_types=1);

namespace Craftzing\Laravel\Abilities\Console;

use Craftzing\Laravel\Abilities\Testing\TestCase;
use Orchestra\Testbench\Concerns\InteractsWithPublishedFiles;
use PHPUnit\Framework\Attributes\Test;

final class AbilityMakeCommandTest extends TestCase
{
    use InteractsWithPublishedFiles;

    /**
     * @var array<int, string>
     */
    protected array $files = [
        'app/Abilities/FooAbility.php',
    ];

    #[Test]
    public function itCanGenerateAbilityFile(): void
    {
        // @phpstan-ignore-next-line method.nonObject
        $this->artisan('make:ability', ['name' => 'FooAbility'])
            ->assertExitCode(0);

        $this->assertFileContains([
            'namespace App\Abilities;',
            'use Craftzing\Laravel\Abilities\Contracts\Ability;',
            'use Illuminate\Foundation\Auth\User;',
            'class FooAbility implements Ability',
        ], 'app/Abilities/FooAbility.php');
    }

    #[Test]
    public function itCanGenerateAbilityFileWithModelOption(): void
    {
        // @phpstan-ignore-next-line method.nonObject
        $this->artisan('make:ability', ['name' => 'FooAbility', '--model' => 'Post'])
            ->assertExitCode(0);

        $this->assertFileContains([
            'namespace App\Abilities;',
            'use App\Models\Post;',
            'use Craftzing\Laravel\Abilities\Contracts\Ability;',
            'use Illuminate\Foundation\Auth\User;',
            'class FooAbility implements Ability',
            'public function __construct',
            'public Post $post',
            'public function granted(User $user)',
        ], 'app/Abilities/FooAbility.php');
    }
}
