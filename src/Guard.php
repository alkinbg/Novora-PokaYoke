<?php

declare(strict_types=1);

namespace Novora\PokaYoke;

final readonly class Guard
{
    /** @param iterable<mixed> $rules */
    public function check(mixed $context, iterable $rules, bool $stopOnFirstViolation = false): GuardResult
    {
        $violations = [];
        foreach ($rules as $rule) {
            if (!$rule instanceof Rule) {
                throw new \InvalidArgumentException('Every rule must implement Rule.');
            }
            $violation = $rule->check($context);
            if ($violation !== null) {
                $violations[] = $violation;
                if ($stopOnFirstViolation) {
                    break;
                }
            }
        }
        return new GuardResult($violations);
    }

    /**
     * @template TResult
     * @param iterable<mixed> $rules
     * @param callable():TResult $action
     * @return TResult
     */
    public function execute(mixed $context, iterable $rules, callable $action): mixed
    {
        $result = $this->check($context, $rules);
        if (!$result->allowed()) {
            throw new OperationBlocked($result);
        }
        return $action();
    }
}
