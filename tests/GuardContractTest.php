<?php

declare(strict_types=1);

namespace Novora\PokaYoke\Tests;

use InvalidArgumentException;
use Novora\PokaYoke\{Guard, GuardResult, OperationBlocked, Rule, Violation};
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class GuardContractTest extends TestCase
{
    public function testInvalidRuleIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new Guard())->check(null, [new \stdClass()]);
    }

    public function testInvalidViolationIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new GuardResult([new \stdClass()]);
    }

    public function testViolationRequiresCodeAndMessage(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Violation('', 'Useful message');
    }

    public function testShortCircuitSkipsRemainingRules(): void
    {
        $calls = 0;
        $rule = new class implements Rule {
            public function check(mixed $context): Violation { return new Violation('blocked', 'Not permitted'); }
        };
        $second = new class($calls) implements Rule {
            public function __construct(private int &$calls) {}
            public function check(mixed $context): ?Violation { ++$this->calls; return null; }
        };
        $result = (new Guard())->check(null, [$rule, $second], true);
        self::assertCount(1, $result->violations);
        self::assertSame(0, $calls);
    }

    public function testRuleExceptionsAreNotSuppressedAndActionIsNotCalled(): void
    {
        $executed = false;
        $rule = new class implements Rule {
            public function check(mixed $context): ?Violation { throw new RuntimeException('Dependency unavailable'); }
        };
        try {
            (new Guard())->execute(null, [$rule], static function () use (&$executed): void { $executed = true; });
            self::fail('Expected rule failure');
        } catch (RuntimeException $exception) {
            self::assertSame('Dependency unavailable', $exception->getMessage());
            self::assertFalse($executed);
        }
    }

    public function testFailingActionPropagatesItsException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Action failed');
        (new Guard())->execute(null, [], static function (): never { throw new RuntimeException('Action failed'); });
    }

    public function testNoRulesAreAllowed(): void
    {
        self::assertTrue((new Guard())->check(null, [])->allowed());
    }
}
