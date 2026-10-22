<?php

declare(strict_types=1);

namespace Novora\PokaYoke;

final class OperationBlocked extends \RuntimeException
{
    public function __construct(public readonly GuardResult $result)
    {
        parent::__construct('Operation blocked by precondition checks.');
    }
}
