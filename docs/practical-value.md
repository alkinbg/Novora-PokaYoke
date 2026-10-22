# Practical value and scope

Poka-Yoke is not a replacement for PHP conditionals, Symfony Validator, or a workflow/state machine. Inline checks are often the better choice for a single precondition.

The library is useful when an application needs several independent checks, ordered diagnostics for the user, and an explicit guard around a business operation. The existing `examples/order.php` demonstrates the central rule: if inventory is insufficient, the action callback is not invoked.

## Example use cases

- **Shipping**: payment confirmed, address present, and stock reserved before dispatch. These conditions must be rechecked under appropriate application-controlled transaction/locking semantics.
- **Manufacturing**: correct part scanned, quality station passed, and safety condition satisfied before marking an operation complete. Physical safety must never depend on a PHP library.
- **Bookings**: consent and capacity checked before confirmation; use a database constraint or transaction to prevent capacity races.

## Comparison with alternatives

- **Plain PHP**: best for a few local, straightforward checks without a need to collect structured diagnostics.
- **Validation frameworks**: best for data shape and field constraint validation.
- **State machines/workflows**: best for managing permissible state transitions.
- **Novora Poka-Yoke**: narrowly focuses on independently supplied preconditions, ordered violation messages, and blocking the action callback.

## Non-guarantees

Checks and actions are executed sequentially in one process, but this does not make them atomic. A rule may have side effects despite the documented convention; the library does not sandbox user code. Do not advertise distributed idempotency, exactly-once execution, financial safety, or concurrency protection. This project is a small compositional convenience, not a general-purpose enterprise rule engine.
