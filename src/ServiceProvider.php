<?php

declare(strict_types=1);

namespace Craftzing\Laravel\Abilities;

use Craftzing\Laravel\Abilities\Console\AbilityMakeCommand;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

final class ServiceProvider extends IlluminateServiceProvider
{
    public function boot(Gate $gate): void
    {
        $gate->before(new AuthoriseUsingAbilities($gate));

        $this->registerCommands();
    }

    private function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                AbilityMakeCommand::class,
            ]);
        }
    }
}
