![Laravel Abilities banner](/art/banner-light.jpg#gh-light-mode-only)
![Laravel Abilities banner](/art/banner-dark.jpg#gh-dark-mode-only)

[![tests](https://github.com/craftzing/laravel-abilities/actions/workflows/tests.yml/badge.svg)](https://github.com/craftzing/laravel-abilities/actions/workflows/tests.yml)
[![static-analysis](https://github.com/craftzing/laravel-abilities/actions/workflows/static-analysis.yml/badge.svg)](https://github.com/craftzing/laravel-abilities/actions/workflows/static-analysis.yml)
[![license](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat&color=4D6CB8)](https://github.com/craftzing/laravel-abilities/blob/master/LICENSE)

Laravel offers loads of fantastic ways to handle authorization, all of which rely on arguments to be passed dynamically
from `Gate` to its callbacks or policies. That dynamic nature, however, requires us to either look up and inspect the
underlying policy or callback to discover its expected arguments, or rely on IDE helpers to do so:
```php
$user->can('update-post', $post);
// or using invokable classes...
$user->can(UpdatePostAbility::class, $post);
```

With this package, we aim at improving this by leveraging on constructor arguments to explicitly expose ability 
dependencies, allowing us to rewrite the above as:
```php
$user->can([new UpdatePostAbility($post)]);
```

# 🔥 Features

- Improved type-hinting using constructor arguments.
- Automatic evaluation of abilities without having to define them in `Gate`.
- Create ability classes with the Artisan CLI.

# 🏁 Getting started

This package requires:
- [PHP](https://www.php.net/supported-versions.php) 8.3

You can install this package using [Composer](https://getcomposer.org) by running the following command:
```shell
composer require craftzing/laravel-abilities
```

# 📚 Usage

To create a class-based ability, implement the `Craftzing\Laravel\Abilities\Ability` interface:
```php
use App\Models\Post;
use Craftzing\Laravel\Abilities\Contracts\Ability;

final readonly class MergePullRequestAbility implements Ability
{
    public function __construct(
        private PullRequest $pullRequest,
    ) {}

    public function granted(mixed $user): bool
    {
        return $user->isMaintainerForRepository($pullRequest->repository);
    }
}
```

> [!TIP]
> You can use the `php artisan make:ability` command to create new abilities.

Once your ability is all setup, you can use it right away. There is no need to define it in `Gate`:
```php
use Illuminate\Support\Facades\Gate;

$user->can([new MergePullRequestAbility($pullRequest)]);
Gate::allows([new MergePullRequestAbility($pullRequest)]);
```

> [!IMPORTANT]
> Note that the **ability MUST be wrapped in an array**. This is due to a technical constraint where `Gate` does not 
> accept object instances to be passed along directly.

## Handling abilities in tests

We highly recommend to unit test all of your ability classes extensively. Doing so will allow you to confidently fake 
ability checks in any functional or feature tests:
```php
use Illuminate\Support\Facades\Gate;

// By explicitly defining the ability class in Gate, the provided callback will
// be called instead of the `granted()` method of the according ability.
Gate::define(MergePullRequestAbility::class, fn (): bool => true);
```

If you want to inspect the arguments that were passed to the ability's constructor, you can pass variables by reference
to the callback:
```php
use Database\Factories\UserFactory;
use Database\Factories\Git\RepositoryFactory;
use Illuminate\Support\Facades\Gate;

// Arrange
$user = UserFactory::new()->create();
$repository = RepositoryFactory::new()->created();
$grantedUser = null;
$grantedAbility = null;
Gate::define(MergePullRequestAbility::class, function (
    mixed $user,
    MergePullRequestAbility $ability,
) use (&$grantedUser, &$grantedAbility): bool {
    $grantedUser = $user;
    $grantedAbility = $ability;
    
    return true;
});

// Act phase of your test...

// Assert
$this->assertInstanceOf(User::class, $grantedUser);
$this->assertTrue($grantedUser->is($user));
$this->assertInstanceOf(MergePullRequestAbility::class, $grantedAbility);
$this->assertTrue($grantedAbility->repository->is($repository));
```

# 📝 Changelog

Check out our [Change log](/CHANGELOG.md) for information on what has changed recently.

# 🤝 How to contribute

Have an idea for a feature? Wanna improve the docs? Found a bug? Check out our [Contributing guide](/CONTRIBUTING.md).

# 💙 Thanks to...

- [The entire Craftzing team](https://craftzing.com)
- [All current and future contributors](https://github.com/creaftzing/laravel-abilities/graphs/contributors)

# 👮 Security

If you discover any security-related issues, please email security@craftzing.com instead of using the issue tracker.

# 🔑 License

The MIT License (MIT). Please see [License File](/LICENSE) for more information.
