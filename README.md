# Novora Poka-Yoke

Small, framework-independent PHP 8.4+ precondition guards for business operations.

> **Status: prototype.** API is experimental; not yet released or published to Packagist.

## Design

- `Rule::check(mixed $context): ?Violation` returns `null` on success or a meaningful failure.
- `Guard::check()` gathers violations, optionally stopping at the first one.
- `Guard::execute()` blocks the action if any rule fails. It returns the action's result if allowed.
- No runtime dependency on Symfony, Laravel, a database, or a web framework.

```php
use Novora\PokaYoke\{Guard, Rule, Violation};

$rule = new class implements Rule {
    public function check(mixed $context): ?Violation {
        return $context['paid'] ? null : new Violation('order.unpaid', 'Payment required.');
    }
};

$order = ['paid' => true];
$result = (new Guard())->execute($order, [$rule], fn () => 'processed');
```

See [`examples/order.php`](examples/order.php).

## Guarantees and limits

Guards reject an operation before invoking its callback if any rule fails. Rules **must not** mutate data. The library does not guarantee atomicity, thread safety, idempotency, concurrency control, or rollback. A change in application state between the checks and the action can invalidate earlier checks. Handle this in the host application using the appropriate transaction and locking strategy.

Exception(s) thrown by a rule or the action propagate to the caller. An empty rule collection permits execution.

## Development

```bash
composer install
composer test
composer analyse
```
