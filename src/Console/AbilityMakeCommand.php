<?php

declare(strict_types=1);

namespace Craftzing\Laravel\Abilities\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Str;
use LogicException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function array_keys;
use function array_values;
use function class_basename;
use function file_exists;
use function is_null;
use function Laravel\Prompts\suggest;
use function preg_quote;
use function preg_replace;
use function str_replace;
use function trim;
use function vsprintf;

#[AsCommand(name: 'make:ability')]
class AbilityMakeCommand extends GeneratorCommand
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'make:ability';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Create a new ability class';

    /**
     * {@inheritdoc}
     */
    protected $type = 'Ability';

    /**
     * {@inheritdoc}
     */
    protected function buildClass($name): string
    {
        $stub = $this->replaceUserNamespace(parent::buildClass($name));

        /** @var string $model */
        $model = $this->option('model');

        return $model ? $this->replaceModel($stub, $model) : $stub;
    }

    private function replaceUserNamespace(string $stub): string
    {
        $model = $this->userProviderModel();

        if (! $model) {
            return $stub;
        }

        return str_replace("{$this->rootNamespace()}User", $model, $stub);
    }

    /**
     * {@inheritdoc}
     */
    protected function userProviderModel(): ?string
    {
        /** @var Repository $config */
        $config = $this->laravel->make(Repository::class);
        $guard = $this->option('guard') ?: $config->get('auth.defaults.guard');

        if (is_null($guardProvider = $config->get("auth.guards.$guard.provider"))) {
            throw new LogicException("The [$guard] guard is not defined in your \"auth\" configuration file.");
        }

        if (! $config->get("auth.providers.$guardProvider.model")) {
            return 'App\\Models\\User';
        }

        return $config->get("auth.providers.$guardProvider.model");
    }

    protected function replaceModel(string $stub, string $model): string
    {
        $model = str_replace('/', '\\', $model);

        if (str_starts_with($model, '\\')) {
            $namespacedModel = trim($model, '\\');
        } else {
            $namespacedModel = $this->qualifyModel($model);
        }

        $model = class_basename(trim($model, '\\'));
        $dummyUser = class_basename($this->userProviderModel() ?: '');
        $dummyModel = Str::camel($model) === 'user' ? 'model' : $model;

        $replace = [
            '{{ namespacedModel }}' => $namespacedModel,
            '{{namespacedModel}}' => $namespacedModel,
            '{{ model }}' => $model,
            '{{model}}' => $model,
            '{{ modelVariable }}' => Str::camel($dummyModel),
            '{{modelVariable}}' => Str::camel($dummyModel),
            '{{ user }}' => $dummyUser,
            '{{user}}' => $dummyUser,
            '$user' => '$' . Str::camel($dummyUser),
        ];

        $stub = str_replace(
            array_keys($replace),
            array_values($replace),
            $stub,
        );

        return (string) preg_replace(
            vsprintf('/use %s;[\r\n]+use %s;/', [
                preg_quote($namespacedModel, '/'),
                preg_quote($namespacedModel, '/'),
            ]),
            "use $namespacedModel;",
            $stub,
        );
    }

    protected function getStub(): string
    {
        return $this->option('model')
            ? $this->resolveStubPath('/stubs/ability.stub')
            : $this->resolveStubPath('/stubs/ability.plain.stub');
    }

    protected function resolveStubPath(string $stub): string
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__ . $stub;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return "$rootNamespace\Abilities";
    }

    /**
     * @return array<int, array<int|string>>
     */
    protected function getOptions(): array
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the ability already exists'],
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'The model that the ability applies to'],
            ['guard', 'g', InputOption::VALUE_OPTIONAL, 'The guard that the ability relies on'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function afterPromptingForMissingArguments(InputInterface $input, OutputInterface $output): void
    {
        if ($this->isReservedName($this->getNameInput()) || $this->didReceiveOptions($input)) {
            return;
        }

        $model = suggest('What model should this ability apply to? (Optional)', $this->possibleModels());

        if ($model) {
            $input->setOption('model', $model);
        }
    }
}
