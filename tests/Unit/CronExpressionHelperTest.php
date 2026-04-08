<?php

declare(strict_types=1);

use Crontinel\Cron\CronExpressionHelper;

it('returns a DateTimeImmutable for nextDue', function (): void {
    $next = CronExpressionHelper::nextDue('* * * * *');
    expect($next)->toBeInstanceOf(DateTimeImmutable::class);
    expect($next)->toBeGreaterThanOrEqual(new DateTimeImmutable);
});

it('returns a DateTimeImmutable for previousDue', function (): void {
    $prev = CronExpressionHelper::previousDue('* * * * *');
    expect($prev)->toBeInstanceOf(DateTimeImmutable::class);
    expect($prev)->toBeLessThanOrEqual(new DateTimeImmutable);
});

it('returns null for an invalid expression', function (): void {
    expect(CronExpressionHelper::nextDue('not-a-cron'))->toBeNull();
    expect(CronExpressionHelper::previousDue('not-a-cron'))->toBeNull();
});

it('detects a late cron', function (): void {
    // Last ran 2 hours ago, every minute cron — previousDue ~60s ago, grace 10s so 60 >= 10 → late
    $lastRunAt = new DateTimeImmutable('-2 hours');
    expect(CronExpressionHelper::isLate($lastRunAt, '* * * * *', 10))->toBeTrue();
});

it('does not flag as late within the grace period', function (): void {
    // Last ran 2 hours ago but grace is 300s — previousDue ~60s ago, 60 < 300 → not yet late
    $lastRunAt = new DateTimeImmutable('-2 hours');
    expect(CronExpressionHelper::isLate($lastRunAt, '* * * * *', 300))->toBeFalse();
});

it('does not flag as late when last run was after the previous due time', function (): void {
    // Last ran 30 seconds ago, expression is every minute — within the current period
    $lastRunAt = new DateTimeImmutable('-30 seconds');
    expect(CronExpressionHelper::isLate($lastRunAt, '* * * * *', 120))->toBeFalse();
});
