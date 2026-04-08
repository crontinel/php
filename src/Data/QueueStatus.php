<?php

declare(strict_types=1);

namespace Crontinel\Data;

final readonly class QueueStatus
{
    public function __construct(
        public string $connection,
        public string $queue,
        public int $depth,
        public int $failedCount,
        public ?int $oldestJobAgeSeconds,
        public int $depthThreshold = 1000,
        public int $waitTimeThresholdSeconds = 300,
    ) {}

    public function isHealthy(): bool
    {
        if ($this->depth > $this->depthThreshold) {
            return false;
        }

        if ($this->oldestJobAgeSeconds !== null && $this->oldestJobAgeSeconds > $this->waitTimeThresholdSeconds) {
            return false;
        }

        return true;
    }
}
