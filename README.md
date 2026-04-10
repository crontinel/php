# crontinel/php

[![Latest Version](https://img.shields.io/packagist/v/crontinel/php.svg)](https://packagist.org/packages/crontinel/php)
[![PHP](https://img.shields.io/badge/PHP-8.2%20%7C%208.3%20%7C%208.4-blue)](https://packagist.org/packages/crontinel/php)
[![License](https://img.shields.io/github/license/crontinel/php.svg)](LICENSE)

Framework-agnostic PHP core for [Crontinel](https://crontinel.com). Provides monitor contracts, typed data objects, alert management with deduplication, and cron expression helpers.

**If you use Laravel, install [`crontinel/laravel`](https://github.com/crontinel/crontinel) instead.** It pulls in this package automatically and adds service providers, dashboard, Artisan commands, and event listeners.

Install `crontinel/php` directly when you are:

- Building an adapter for Symfony, Slim, or another framework
- Integrating Crontinel into a vanilla PHP application
- Writing a custom monitor that does not depend on Laravel

---

## Requirements

- PHP 8.2, 8.3, or 8.4

---

## Installation

```bash
composer require crontinel/php
```

---

## What this package provides

### Contracts

- **`MonitorInterface`** -- implement `isHealthy(): bool` to create a custom monitor
- **`AlertChannelInterface`** -- implement `send(AlertEvent $event): void` to deliver alerts via any transport (Slack, email, webhook, etc.)

### Data objects

Immutable, readonly value objects used across the stack:

- **`AlertEvent`** -- key, title, message, level (`critical` / `warning` / `info` / `resolved`), fired timestamp
- **`CronStatus`** -- command, expression, status, last run metadata, next due time
- **`HorizonStatus`** -- Horizon supervisor state
- **`QueueStatus`** -- queue depth, failed count, oldest job age

### Alert management

**`AlertManager`** handles fire/resolve lifecycle with built-in deduplication. It accepts any PSR-16 cache and any `AlertChannelInterface` implementation:

- Deduplicates repeated alerts for the same key (default: 5 min TTL)
- Auto-sends "resolved" notifications when an issue clears
- Logs failures via PSR-3 logger (falls back to `NullLogger`)

### Cron expression helpers

**`CronExpressionHelper`** wraps `dragonmantank/cron-expression` with convenience methods:

- `nextDue(string $expression)` -- next scheduled run as `DateTimeImmutable`
- `previousDue(string $expression)` -- last scheduled run
- `isLate(DateTimeInterface $lastRunAt, string $expression, int $graceSeconds = 120)` -- whether a job has missed its window

### Built-in monitors

Ready-to-use monitor implementations (all implement `MonitorInterface`):

- `CronMonitor`, `HorizonMonitor`, `QueueMonitor`

---

## Usage example

Implement `MonitorInterface` to build a custom health check:

```php
use Crontinel\Contracts\MonitorInterface;

final class DiskSpaceMonitor implements MonitorInterface
{
    public function __construct(
        private readonly string $path = '/',
        private readonly float $minFreePercent = 10.0,
    ) {}

    public function isHealthy(): bool
    {
        $free = disk_free_space($this->path);
        $total = disk_total_space($this->path);

        return ($free / $total) * 100 >= $this->minFreePercent;
    }
}
```

Wire up alert delivery by implementing `AlertChannelInterface`:

```php
use Crontinel\Contracts\AlertChannelInterface;
use Crontinel\Data\AlertEvent;

final class WebhookChannel implements AlertChannelInterface
{
    public function __construct(
        private readonly string $url,
    ) {}

    public function send(AlertEvent $event): void
    {
        file_get_contents($this->url, false, stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/json',
                'content' => json_encode([
                    'key'      => $event->key,
                    'title'    => $event->title,
                    'message'  => $event->message,
                    'level'    => $event->level,
                    'resolved' => $event->resolved,
                ]),
            ],
        ]));
    }
}
```

Then use `AlertManager` to fire and resolve alerts:

```php
use Crontinel\Alert\AlertManager;

// Requires any PSR-16 cache implementation
$alertManager = new AlertManager(
    cache: $cache,
    channel: new WebhookChannel('https://example.com/webhook'),
);

$monitor = new DiskSpaceMonitor('/data');

if (! $monitor->isHealthy()) {
    $alertManager->fire('disk.data', 'Low disk space', 'Disk /data is below 10% free', 'critical');
} else {
    $alertManager->resolve('disk.data');
}
```

---

## Ecosystem

| Package | Description |
|---|---|
| [crontinel/php](https://github.com/crontinel/php) (this repo) | Framework-agnostic PHP core |
| [crontinel/laravel](https://github.com/crontinel/crontinel) | Laravel package with dashboard, Artisan commands, and event listeners |
| [crontinel/mcp-server](https://github.com/crontinel/mcp-server) | MCP server for AI assistants (Claude, Cursor) |
| [docs.crontinel.com](https://docs.crontinel.com) | Full documentation |
| [app.crontinel.com](https://app.crontinel.com) | Hosted SaaS dashboard |

---

## License

MIT. See [LICENSE](LICENSE).

Built by [Harun R Rayhan](https://github.com/HarunRRayhan) · [crontinel.com](https://crontinel.com)
