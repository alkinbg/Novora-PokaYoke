# Contributing

Thank you for considering a contribution to Novora Poka-Yoke.

## Before opening a pull request

For larger changes, please start with an issue describing the problem, proposed API, alternatives, and compatibility implications. The library intentionally has a small, framework-independent scope. Avoid introducing required framework, database or container dependencies.

## Development

Requirements: PHP 8.4+, Composer 2.

```bash
composer install
composer validate --strict
composer test
composer analyse
php examples/order.php
```

Please add regression tests for changed behavior and keep the README and examples consistent. Explain any behavior or public API change in your pull request. Avoid unrelated refactoring.

## Reviews

CI must pass on supported PHP versions. A green build does not replace review of edge cases, side effects, and documented guarantees. Changes should be reviewed before merging into `main`.

## Reporting security issues

Please follow [SECURITY.md](SECURITY.md) rather than publishing an exploitable issue.
