<?php

declare(strict_types=1);

namespace Novora\PokaYoke\Tests;

use Novora\PokaYoke\{Guard, OperationBlocked, Rule, Violation};
use PHPUnit\Framework\TestCase;

final class GuardTest extends TestCase
{
    public function testAllFailuresAreCollected(): void
    {
        $rule = static fn (string $code): Rule => new class($code) implements Rule {
            public function __construct(private string $code) {}
            public function check(mixed $context): ?Violation { return new Violation($this->code, 'Blocked'); }
        };
        $result = (new Guard())->check(null, [$rule('one'), $rule('two')]);
        self::assertFalse($result->allowed());
        self::assertSame(['one', 'two'], array_map(static fn (Violation $v): string => $v->code, $result->violations));
        self::assertCount(1, (new Guard())->check(null, [$rule('one'), $rule('two')], true)->violations);
    }

    public function testBlockedOperationNeverExecutesAction(): void
    {
        $executed = false;
        $rule = new class implements Rule {
            public function check(mixed $context): ?Violation { return new Violation('blocked', 'Not ready'); }
        };
        try {
            (new Guard())->execute(null, [$rule], static function () use (&$executed): void { $executed = true; });
            self::fail('Operation should be blocked');
        } catch (OperationBlocked $exception) {
            self::assertFalse($executed);
            self::assertSame('blocked', $exception->result->violations[0]->code);
        }
    }

    public function testSuccessfulOperationReturnsValue(): void
    {
        self::assertSame(42, (new Guard())->execute(null, [], static fn (): int => 42));
    }
}
