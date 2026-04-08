<?php

declare(strict_types=1);

namespace Crontinel\Data;

final readonly class HorizonStatus
{
    public function __construct(
        public bool $running,
        public array $supervisors,
        public float $failedJobsPerMinute,
        public ?\DateTimeInterface $pausedAt,
        public float $failedJobsPerMinuteThreshold = 5.0,
    ) {}

    public function isHealthy(): bool
    {
        return $this->running
            && $this->pausedAt === null
            && $this->failedJobsPerMinute < $this->failedJobsPerMinuteThreshold;
    }
}
