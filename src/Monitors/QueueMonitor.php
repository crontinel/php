<?php

declare(strict_types=1);

namespace Crontinel\Monitors;

use Crontinel\Contracts\MonitorInterface;
use Crontinel\Data\QueueStatus;

class QueueMonitor implements MonitorInterface
{
    public function __construct(private readonly array $config = []) {}

    public function isHealthy(): bool
    {
        return true; // Callers check individual QueueStatus objects
    }

    /**
     * Build a QueueStatus from pre-fetched raw data (no framework I/O).
     */
    public function statusFromData(
        string $connection,
        string $queue,
        int $depth,
        int $failedCount,
        ?int $oldestJobAgeSeconds,
    ): QueueStatus {
        return new QueueStatus(
            connection: $connection,
            queue: $queue,
            depth: $depth,
            failedCount: $failedCount,
            oldestJobAgeSeconds: $oldestJobAgeSeconds,
            depthThreshold: (int) ($this->config['depth_alert_threshold'] ?? 1000),
            waitTimeThresholdSeconds: (int) ($this->config['wait_time_alert_seconds'] ?? 300),
        );
    }
}
