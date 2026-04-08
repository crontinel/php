<?php

declare(strict_types=1);

namespace Crontinel\Data;

final readonly class CronStatus
{
    public function __construct(
        public string $command,
        public string $expression,
        public string $status, // 'ok' | 'failed' | 'late' | 'never_run'
        public ?\DateTimeInterface $lastRunAt,
        public ?int $lastExitCode,
        public ?int $lastDurationMs,
        public ?\DateTimeInterface $nextDueAt,
    ) {}

    public function isHealthy(): bool
    {
        return $this->status === 'ok';
    }
}
