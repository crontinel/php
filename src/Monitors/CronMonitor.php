<?php

declare(strict_types=1);

namespace Crontinel\Monitors;

use Crontinel\Contracts\MonitorInterface;
use Crontinel\Cron\CronExpressionHelper;
use Crontinel\Data\CronStatus;

class CronMonitor implements MonitorInterface
{
    public function __construct(private readonly array $config = []) {}

    public function isHealthy(): bool
    {
        return true; // Aggregate health — callers check individual statusFor() results
    }

    /**
     * Compute a CronStatus from pre-fetched data (no framework I/O).
     *
     * @param  array{exit_code: int, ran_at: string, duration_ms: int}|null  $lastRun
     */
    public function statusFor(
        string $command,
        string $expression,
        ?array $lastRun = null,
    ): CronStatus {
        $lastRunAt = isset($lastRun['ran_at'])
            ? new \DateTimeImmutable($lastRun['ran_at'])
            : null;

        $lastExitCode = isset($lastRun['exit_code']) ? (int) $lastRun['exit_code'] : null;
        $lastDurationMs = isset($lastRun['duration_ms']) ? (int) $lastRun['duration_ms'] : null;
        $previousDue = $expression !== '' ? CronExpressionHelper::previousDue($expression) : null;

        $status = match (true) {
            $lastRun === null => 'never_run',
            $lastExitCode !== 0 => 'failed',
            $this->isLate($lastRunAt, $previousDue) => 'late',
            default => 'ok',
        };

        return new CronStatus(
            command: $command,
            expression: $expression,
            status: $status,
            lastRunAt: $lastRunAt,
            lastExitCode: $lastExitCode,
            lastDurationMs: $lastDurationMs,
            nextDueAt: CronExpressionHelper::nextDue($expression),
        );
    }

    private function isLate(?\DateTimeImmutable $lastRunAt, ?\DateTimeImmutable $previousDue): bool
    {
        if ($lastRunAt === null || $previousDue === null) {
            return false;
        }

        if ($lastRunAt >= $previousDue) {
            return false;
        }

        $threshold = (int) ($this->config['late_alert_after_seconds'] ?? 120);
        $now = new \DateTimeImmutable;

        return ($now->getTimestamp() - $previousDue->getTimestamp()) > $threshold;
    }
}
