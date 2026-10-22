<?php

declare(strict_types=1);

namespace Novora\PokaYoke;

final readonly class GuardResult
{
    /** @param list<mixed> $violations */
    public function __construct(public array $violations)
    {
        foreach ($violations as $violation) {
            if (!$violation instanceof Violation) {
                throw new \InvalidArgumentException('Only Violation objects are accepted.');
            }
        }
    }

    public function allowed(): bool
    {
        return $this->violations === [];
    }
}
