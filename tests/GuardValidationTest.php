<?php

declare(strict_types=1);

namespace Novora\PokaYoke\Tests;

use Novora\PokaYoke\{Guard, OperationBlocked, Rule, Violation};
use PHPUnit\Framework\TestCase;

final class GuardValidationTest extends TestCase
{
    public function testResultDoesNotLoseFalseZeroAndNullContexts(): void
    {
        $contexts = [false, 0, null, '', []];
        foreach ($contexts as $context) {
            $rule = new class($context) implements Rule {
                public function __construct(private readonly mixed $expected) {}
                public function check(mixed $context): ?Violation
                {
                    return $context === $this->expected ? null : new Violation('context.mismatch', 'Context changed.');
                }
            };
            self::assertTrue((new Guard())->check($context, [$rule])->allowed());
        }
    }

    public function testMultipleViolationsRemainOrderedInBlockException(): void
    {
        $make = static fn (string $code): Rule => new class($code) implements Rule {
            public function __construct(private readonly string $code) {}
            public function check(mixed $context): Violation
            {
                return new Violation($this->code, 'Blocked.');
            }
        };
        try {
            (new Guard())->execute(null, [$make('first'), $make('second')], static fn () => 'never');
            self::fail('Should throw OperationBlocked.');
        } catch (OperationBlocked $e) {
            self::assertSame(['first', 'second'], array_map(static fn (Violation $v): string => $v->code, $e->result->violations));
        }
    }
}
