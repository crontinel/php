<?php

declare(strict_types=1);

use Crontinel\Data\HorizonStatus;

it('is healthy when running with no failures', function (): void {
    $status = new HorizonStatus(running: true, supervisors: [], failedJobsPerMinute: 0.0, pausedAt: null);
    expect($status->isHealthy())->toBeTrue();
});

it('is not healthy when not running', function (): void {
    $status = new HorizonStatus(running: false, supervisors: [], failedJobsPerMinute: 0.0, pausedAt: null);
    expect($status->isHealthy())->toBeFalse();
});

it('is not healthy when paused', function (): void {
    $status = new HorizonStatus(running: true, supervisors: [], failedJobsPerMinute: 0.0, pausedAt: new DateTimeImmutable);
    expect($status->isHealthy())->toBeFalse();
});

it('is not healthy when failed jobs per minute exceeds threshold', function (): void {
    $status = new HorizonStatus(running: true, supervisors: [], failedJobsPerMinute: 10.0, pausedAt: null, failedJobsPerMinuteThreshold: 5.0);
    expect($status->isHealthy())->toBeFalse();
});

it('uses custom threshold', function (): void {
    $status = new HorizonStatus(running: true, supervisors: [], failedJobsPerMinute: 3.0, pausedAt: null, failedJobsPerMinuteThreshold: 2.0);
    expect($status->isHealthy())->toBeFalse();
});
