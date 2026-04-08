<?php

declare(strict_types=1);

use Crontinel\Monitors\CronMonitor;

it('returns never_run status when no last run data', function () {
    $monitor = new CronMonitor;

    $status = $monitor->statusFor('send:invoices', '0 9 * * *', null);

    expect($status->status)->toBe('never_run')
        ->and($status->isHealthy())->toBeFalse();
});

it('returns failed status when exit code is non-zero', function () {
    $monitor = new CronMonitor;

    $status = $monitor->statusFor('send:invoices', '0 9 * * *', [
        'exit_code' => 1,
        'ran_at' => (new DateTimeImmutable('-1 hour'))->format(DateTimeInterface::ATOM),
        'duration_ms' => 50,
    ]);

    expect($status->status)->toBe('failed')
        ->and($status->isHealthy())->toBeFalse();
});

it('returns ok status when exit code is zero and ran recently', function () {
    $monitor = new CronMonitor;

    $status = $monitor->statusFor('send:invoices', '0 9 * * *', [
        'exit_code' => 0,
        'ran_at' => (new DateTimeImmutable('-30 minutes'))->format(DateTimeInterface::ATOM),
        'duration_ms' => 200,
    ]);

    expect($status->status)->toBeIn(['ok', 'late'])
        ->and($status->command)->toBe('send:invoices');
});

it('populates next due date', function () {
    $monitor = new CronMonitor;

    $status = $monitor->statusFor('prune:data', '0 2 * * *', null);

    expect($status->nextDueAt)->toBeInstanceOf(DateTimeImmutable::class);
});
