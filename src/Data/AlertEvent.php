<?php

declare(strict_types=1);

namespace Crontinel\Data;

final readonly class AlertEvent
{
    public function __construct(
        public string $key,
        public string $title,
        public string $message,
        public string $level, // 'critical' | 'warning' | 'info' | 'resolved'
        public bool $resolved,
        public \DateTimeImmutable $firedAt,
    ) {}
}
