<?php

declare(strict_types=1);

namespace Novora\PokaYoke\Tests;

use Novora\PokaYoke\{Guard, Rule, Violation};
use PHPUnit\Framework\TestCase;

final class GuardEdgeCasesTest extends TestCase
{
    public function testRulesReceiveOriginalContextInOrder(): void
    {
        $context = new \stdClass();
        $recorder = new \stdClass();
        $recorder->seen = [];
        $rule = new class($recorder) implements Rule {
            public function __construct(private \stdClass $recorder) {}
            public function check(mixed $context): ?Violation {
                $this->recorder->seen[] = $context;
                return null;
            }
        };
        self::assertTrue((new Guard())->check($context, [$rule, $rule])->allowed());
        self::assertSame([$context, $context], $recorder->seen);
    }

    public function testActionReceivesNoImplicitArguments(): void
    {
        $called = false;
        $result = (new Guard())->execute('context', [], static function () use (&$called): string {
            $called = true;
            return 'done';
        });
        self::assertTrue($called);
        self::assertSame('done', $result);
    }

    public function testGuardRejectsInvalidLaterRuleBeforeActionRuns(): void
    {
        $called = false;
        try {
            (new Guard())->execute(null, [new class implements Rule {
                public function check(mixed $context): ?Violation { return null; }
            }, new \stdClass()], static function () use (&$called): void { $called = true; });
            self::fail('Invalid second rule must throw.');
        } catch (\InvalidArgumentException) {
            self::assertFalse($called);
        }
    }
}
