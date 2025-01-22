Usage
===

This section will guide you through the basic usage of this package. By the end of this section, you should be able to
use your own abilities.

## Implementations

```php
<?php

declare(strict_types=1);

namespace App;

use Craftzing\Laravel\Abilities\Contracts\Ability;

final class Administer implements Ability
{
    public function granted(mixed $user): bool
    {
        return $user->isAdmin();
    }
}
```

It is also possible to use them inside other abilities:
```php
<?php

declare(strict_types=1);

namespace App;

use Craftzing\Laravel\Abilities\Contracts\Ability;

final readonly class ViewResource implements Ability
{
    public function __construct(private mixed $resource) {}
    
    public function granted(mixed $user): bool
    {
        return $user->can([new Administer()]) ?: $this->resource->createdBy($this->user);
    }
}
```

## Execution

```php
<?php

declare(strict_types=1);

namespace App;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

final readonly class ViewOrderRequestHandler
{
    use AuthorizesRequests;
    
    public function __invoke(Request $request, Order $resource): Response
    {     
        $this->authorize([new ViewResource($resource)]);
        
        // omitted for brevity
    }
}
```

```php
<?php

declare(strict_types=1);

namespace App;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

final readonly class ViewOrderRequestHandler
{    
    public function __invoke(Request $request, Order $resource): Response
    {     
        $user->can([new ViewResource($resource)]) or throw new AuthorizationException();
        
        // omitted for brevity
    }
}
```

## Testing

```php
<?php

declare(strict_types=1);

namespace Tests;

use App\ViewResource;use Craftzing\Laravel\Abilities\Testing\Concerns\FakesAbilities;use Database\Factories\OrderFactory;use Database\Factories\UserFactory;use PHPUnit\Framework\Attributes\Test;

final class ViewOrderRequestHandler extends TestCase
{
    use FakesAbilities;
    
    #[Test]
    public function userNeedsAccessToViewResource(): void
    {
        $this->deny(ViewResource::class);
        $order = OrderFactory::new()->create();
        $user = UserFactory::new()->create();
        
        $response = $this->actAsUser($user)->get("orders/{$order->id()}");
        
        $response->assertForbidden();
    }
    
    #[Test]
    public function itCanReturnTheOrder(): void
    {
        $this->allow(ViewResource::class);
        $order = OrderFactory::new()->create();
        $user = UserFactory::new()->create();
        
        $response = $this->actAsUser($user)->get("orders/{$order->id()}");
        
        $response->assertOk();
        // omitted for brevity
    }
}
```

> 💡 Found an issue or is this section missing anything? Feel free to open a
> [PR](https://github.com/craftzing/laravel-abilities/compare) or
> [issue](https://github.com/craftzing/laravel-abilities/issues/new).

---

[⏪ Getting started](getting-started.md)
