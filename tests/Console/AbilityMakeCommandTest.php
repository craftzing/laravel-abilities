<?php

declare(strict_types=1);

namespace Craftzing\Laravel\Tests\Abilities\Console;

use Craftzing\Laravel\Tests\Abilities\TestCase;
use Orchestra\Testbench\Concerns\InteractsWithPublishedFiles;

class AbilityMakeCommandTest extends TestCase
{
    use InteractsWithPublishedFiles;

    /**
     * @var array<int, string>
     */
    protected array $files = [
        'app/Abilities/FooAbility.php',
    ];

    public function testItCanGenerateAbilityFile(): void
    {
        $this->artisan('make:ability', ['name' => 'FooAbility'])
            ->assertExitCode(0);

        $this->assertFileContains([
            'namespace App\Abilities;',
            'use Craftzing\Laravel\Abilities\Contracts\Ability;',
            'use Illuminate\Foundation\Auth\User;',
            'class FooAbility implements Ability',
        ], 'app/Abilities/FooAbility.php');
    }

    public function testItCanGenerateAbilityFileWithModelOption(): void
    {
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