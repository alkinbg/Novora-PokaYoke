<?php

declare(strict_types=1);

require dirname(__DIR__).'/vendor/autoload.php';

use Novora\PokaYoke\{Guard, OperationBlocked, Rule, Violation};

$order = ['paid' => true, 'stock' => 0];
$hasStock = new class implements Rule {
    public function check(mixed $context): ?Violation
    {
        return $context['stock'] > 0 ? null : new Violation('stock.empty', 'Insufficient stock.');
    }
};

try {
    (new Guard())->execute($order, [$hasStock], static fn (): string => 'Dispatched');
} catch (OperationBlocked $exception) {
    foreach ($exception->result->violations as $violation) {
        echo $violation->code.': '.$violation->message.PHP_EOL;
    }
}
