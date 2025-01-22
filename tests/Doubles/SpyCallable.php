<?php

declare(strict_types=1);

namespace Craftzing\Laravel\Tests\Abilities\Doubles;

use Exception;
use PHPUnit\Framework\Assert;

use function call_user_func_array;
use function is_callable;

/**
 * @template InvocationArguments as array<int, mixed>
 */
final class SpyCallable
{
    /**
     * @var InvocationArguments[]
     */
    private array $invocations = [];

    public function __construct(
        public readonly mixed $returnValueWhenCalled = null,
        public readonly ?Exception $exceptionWhenCalled = null,
    ) {}

    public static function throwExceptionWhenCalled(?Exception $exceptionWhenCalled = null): self
    {
        return new self(
            null,
            $exceptionWhenCalled ?: new Exception('SpyCallable configured to fail when called.'),
        );
    }

    public function __invoke(...$arguments): mixed
    {
        $this->invocations[] = $arguments;

        if ($this->exceptionWhenCalled) {
            throw $this->exceptionWhenCalled;
        }

        if (is_callable($this->returnValueWhenCalled)) {
            return call_user_func_array($this->returnValueWhenCalled, $arguments);
        }

        return $this->returnValueWhenCalled;
    }

    public function assertWasCalled(?callable $assertions = null, string $message = ''): void
    {
        if ($message !== '') {
            $message = "\n$message";
        }

        Assert::assertNotEmpty($this->invocations, "SpyCallable was not called as expected.$message");

        if ($assertions === null) {
            return;
        }

        foreach ($this->invocations as $invocation) {
            $assertions(...$invocation);
        }
    }

    public function assertWasCalledTimes(int $amount): void
    {
        Assert::assertCount($amount, $this->invocations);
    }

    public function assertWasCalledOnce(): void
    {
        $this->assertWasCalledTimes(1);
    }

    public function assertWasCalledOnceWithArguments(...$expectedArguments): void
    {
        $matchingInvocations = [];

        foreach ($this->invocations as $invocation) {
            if ($invocation === $expectedArguments) {
                $matchingInvocations[] = $invocation;
            }
        }

        Assert::assertNotEmpty(
            $matchingInvocations,
            'SpyCallable was never called with the provided arguments.',
        );
        Assert::assertCount(
            1,
            $matchingInvocations,
            'SpyCallable was called multiple times with the provided arguments. Expected to be called only once.',
        );
    }

    public function assertWasCalledOnceWithEqualArguments(...$expectedArguments): void
    {
        $matchingInvocations = [];

        foreach ($this->invocations as $invocation) {
            if ($invocation === $expectedArguments) {
                $matchingInvocations[] = $invocation;
            }
        }

        Assert::assertNotEmpty(
            $matchingInvocations,
            'SpyCallable was never called with the provided arguments.',
        );
        Assert::assertCount(
            1,
            $matchingInvocations,
            'SpyCallable was called multiple times with the provided arguments. Expected to be called only once.',
        );
    }

    public function assertWasNotCalled(): void
    {
        Assert::assertEmpty($this->invocations, 'SpyCallable was called unexpectedly.');
    }
}