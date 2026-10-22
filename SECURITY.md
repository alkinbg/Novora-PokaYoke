# Security Policy

## Supported versions

Only the latest stable 1.x release will receive security fixes after the first release. Pre-release development branches are not covered by a security support guarantee.

## Reporting a vulnerability

Please use GitHub's **Report a vulnerability** / private vulnerability reporting feature in the repository's Security tab when available. Do not publish an exploit or sensitive proof-of-concept in a public issue. If private reporting is unavailable, open a public issue requesting a private contact channel **without sharing vulnerability details**.

Include affected versions, the scenario, impact and reproduction steps through the private channel. The maintainer will assess the report and coordinate a fix and disclosure when appropriate. Response times are best-effort and no response-time SLA is promised.

This library executes application-provided rules and callbacks. It does not provide transaction isolation, distributed locking, idempotency, or rollback. See the README for these design limits.
