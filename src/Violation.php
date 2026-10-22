<?php

declare(strict_types=1);

namespace Novora\PokaYoke;

final readonly class Violation
{
    public function __construct(public string $code, public string $message)
    {
        if ($code === '' || $message === '') {
            throw new \InvalidArgumentException('Violation code and message must be non-empty.');
        }
    }
}
