# Release readiness audit

## Pass 1: API and architecture

The core is intentionally framework-independent. The public contract is `Rule::check(mixed): ?Violation`, `Guard::check()` and `Guard::execute()`. All rules must be side-effect-free by convention, but the library cannot enforce this. `mixed` context makes integration easy at the cost of compile-time type safety; a typed generic API is a design decision to settle before 1.0.

## Pass 2: Failure scenarios

Tests cover rule ordering, collect-all and fail-fast evaluation, invalid rule objects and violation payloads, blocked execution, exceptions thrown by rule and action, and allowed operations. A failing rule prevents the operation callback from being invoked. `execute()` has no transaction/locking guarantees; concurrent state changes can invalidate check results.

## Pass 3: Distribution / final checks

Check Composer metadata and autoloading, PHP 8.4 and 8.5 CI, clean install in a separate consumer project, README consistency, license and tag alignment. Do not tag or publish until the checks have passed and the user approves.

## Open release decision

The library is still a prototype. There is no idempotency guarantee, atomic precondition enforcement, or durable state. This limitation must remain explicit in docs and examples. The generic context API and guard outcome naming need a final public-API review before 1.0.
