# Novora Poka-Yoke

Small, framework-independent PHP 8.4+ precondition guards for business operations.

> **Current stable release: v1.0.0.** Available on [Packagist](https://packagist.org/packages/novora/poka-yoke). The package provides a deliberately small, synchronous precondition-checking API, not a general-purpose workflow or rule engine.

## Installation

```bash
composer require "novora/poka-yoke:^1.0"
```

## Requirements

- PHP 8.4 or newer (within the supported PHP 8 major series).
- Composer 2.
- No mandatory external runtime packages.

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

## When to use it

Choose this library when you want to check application-defined preconditions, collect actionable violations, and prevent an operation callback from running when any check fails. For ordinary data validation, prefer an established validation library. For workflow state transitions, use a workflow/state-machine library. This package intentionally does not replicate their features.

## Contract and design decisions

The `mixed` context is intentional: independent business applications can pass an immutable DTO, an entity, or scalar context without an adapter. For typed domain rules, validate the incoming context explicitly or delegate to a typed service. `Guard::execute()` invokes the zero-argument action only after all rules accept the context. Keep rule evaluation observational: rule implementations must not mutate state. A failure result is represented by a stable machine-readable violation `code` and a human-readable `message`; callers control localization and presentation.

The default is to evaluate all rules and collect every violation in the supplied order. Pass `stopOnFirstViolation: true` to `check()` when later checks should be skipped. The current API deliberately does **not** ship a rules registry, HTTP middleware, state machine, or concurrency layer.

## Guarantees and limits

Guards reject an operation before invoking its callback if any rule fails. Rules **must not** mutate data. The library does not guarantee atomicity, thread safety, idempotency, concurrency control, or rollback. A change in application state between the checks and the action can invalidate earlier checks. Handle this in the host application using the appropriate transaction and locking strategy.

Exception(s) thrown by a rule or the action propagate to the caller. An empty rule collection permits execution.

## Development

```bash
composer install
composer test
composer analyse
```

## Error handling

`Guard::check()` returns a `GuardResult` containing all violations in rule order, or just the first when `stopOnFirstViolation` is enabled. `Guard::execute()` throws `OperationBlocked` with the same result when rules reject an operation. Invalid rule objects, invalid violations and empty violation codes/messages are rejected with `InvalidArgumentException`.

If a rule itself throws (for example, because its data source is unavailable), that exception propagates and the action does not run. An exception raised by the action also propagates; the library cannot roll back its side effects. An empty rule list is intentionally treated as permitted: applications that require mandatory checks must supply them.

For practical scenarios and comparison with alternatives, see [Practical value and scope](docs/practical-value.md).
