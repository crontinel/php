# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.1.0] - 2026-04-?? (Initial Release)

### Added
- `MonitorInterface` contract for custom health checks
- `AlertChannelInterface` contract for alert delivery transports
- `AlertManager` with deduplication and auto-resolve lifecycle
- `AlertEvent` data object (key, title, message, level, timestamp)
- `CronStatus` and `HorizonStatus` data objects
- `QueueStatus` data object (depth, failed count, oldest job age)
- `CronExpressionHelper` with `nextDue`, `previousDue`, `isLate` methods
- Built-in `CronMonitor`, `HorizonMonitor`, `QueueMonitor` implementations
- `DiskSpaceMonitor` example custom monitor
- MIT license

### Notes
- Requires PHP 8.2+
- Requires `dragonmantank/cron-expression` ^3.3
- Requires PSR-16 cache and PSR-3 logger implementations for `AlertManager`
- Compatible with Laravel, Symfony, Slim, and framework-agnostic setups
- Laravel integration is provided by the separate [`crontinel/laravel`](https://github.com/crontinel/crontinel) package
