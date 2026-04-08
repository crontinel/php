# crontinel/php

[![Latest Version](https://img.shields.io/packagist/v/crontinel/php.svg)](https://packagist.org/packages/crontinel/php)
[![PHP](https://img.shields.io/badge/PHP-8.2%20%7C%208.3%20%7C%208.4-blue)](https://packagist.org/packages/crontinel/php)
[![License](https://img.shields.io/github/license/crontinel/php.svg)](LICENSE)

Framework-agnostic PHP core for [Crontinel](https://crontinel.com) — data objects, monitor contracts, and alert management.

This package is the shared foundation used by `crontinel/laravel`. You do not need to install it directly unless you are building a framework adapter.

---

## Requirements

- PHP 8.2, 8.3, or 8.4

---

## Installation

```bash
composer require crontinel/php
```

---

## Usage

If you are using Laravel, install [`crontinel/laravel`](https://github.com/crontinel/crontinel) instead — it composes this package automatically.

This package provides:

- **Monitor contracts** (`MonitorInterface`, `MonitorResultInterface`) — implement to build custom monitors
- **Data objects** (`MonitorResult`, `AlertPayload`) — typed value objects passed between layers
- **Alert management** (`AlertService`) — deduplication, resolution tracking, and dispatch
- **Cron expression helpers** via `dragonmantank/cron-expression`

---

## Ecosystem

| Repo | Description |
|---|---|
| [crontinel/php](https://github.com/crontinel/php) (this repo) | Framework-agnostic PHP core |
| [crontinel/laravel](https://github.com/crontinel/crontinel) | Laravel package that depends on this library |
| [docs.crontinel.com](https://docs.crontinel.com) | Full documentation |

---

## License

MIT — free forever. See [LICENSE](LICENSE).

Built by [Harun R Rayhan](https://github.com/HarunRRayhan) · [crontinel.com](https://crontinel.com)
