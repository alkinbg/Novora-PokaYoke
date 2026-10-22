<?php

declare(strict_types=1);

namespace Novora\PokaYoke;

/** Implementations should inspect context without causing side effects. */
interface Rule
{
    public function check(mixed $context): ?Violation;
}
