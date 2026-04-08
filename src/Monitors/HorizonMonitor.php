<?php

declare(strict_types=1);

namespace Crontinel\Monitors;

use Crontinel\Contracts\MonitorInterface;
use Crontinel\Data\HorizonStatus;

class HorizonMonitor implements MonitorInterface
{
    public function __construct(private readonly array $config = []) {}

    public function isHealthy(): bool
    {
        return true; // Callers check the returned HorizonStatus
    }

    /**
     * Build a HorizonStatus from pre-fetched raw data (no framework I/O).
     *
     * @param  array<int, array{name: string, status: string, processes: int, queue: string}>  $supervisors
     */
    public function statusFromData(
        string $masterStatus,
        array $supervisors,
        float $failedJobsPerMinute,
    ): HorizonStatus {
        $threshold = (float) ($this->config['failed_jobs_per_minute_threshold'] ?? 5.0);

        return new HorizonStatus(
            running: $masterStatus === 'running',
            supervisors: $supervisors,
            failedJobsPerMinute: $failedJobsPerMinute,
            pausedAt: $masterStatus === 'paused' ? new \DateTimeImmutable : null,
            failedJobsPerMinuteThreshold: $threshold,
        );
    }
}
