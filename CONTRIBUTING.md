# Contributing to crontinel/php

The `crontinel/php` package is the framework-agnostic core of Crontinel. Contributions are welcome.

## Getting started

```bash
git clone https://github.com/crontinel/php.git
cd php
composer install
```

Run the test suite:

```bash
./vendor/bin/phpunit
```

## How to submit changes

1. Fork and create a branch from `main`: `git checkout -b fix/your-fix-name`
2. Make your changes. Bug fixes should include a test.
3. Run `./vendor/bin/phpunit` to confirm all tests pass.
4. Run `./vendor/bin/pint` to format your PHP code (PSR-12).
5. Open a pull request against `main` with a clear description of what changed and why.

## Code style

PSR-12. Run Pint before committing:

```bash
./vendor/bin/pint
```

## Reporting issues

Open a GitHub issue with:
- What you expected
- What happened instead
- Steps to reproduce
- PHP version

## Scope

This package provides the core ping/check-in logic without any framework dependency. Keep contributions framework-agnostic. Laravel-specific features belong in `crontinel/laravel` instead.
