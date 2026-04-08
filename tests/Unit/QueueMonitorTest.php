<?php

declare(strict_types=1);

use Crontinel\Monitors\QueueMonitor;

it('returns healthy status within thresholds', function () {
    $monitor = new QueueMonitor(['depth_alert_threshold' => 1000, 'wait_time_alert_seconds' => 300]);

    $status = $monitor->statusFromData('redis', 'default', 50, 0, 10);

    expect($status->isHealthy())->toBeTrue();
});

it('returns unhealthy status when depth exceeds threshold', function () {
    $monitor = new QueueMonitor(['depth_alert_threshold' => 500]);

    $status = $monitor->statusFromData('redis', 'default', 600, 0, null);

    expect($status->isHealthy())->toBeFalse();
});

it('returns unhealthy status when oldest job age exceeds threshold', function () {
    $monitor = new QueueMonitor(['wait_time_alert_seconds' => 120]);

    $status = $monitor->statusFromData('redis', 'emails', 5, 0, 300);

    expect($status->isHealthy())->toBeFalse();
});

it('populates all fields correctly', function () {
    $monitor = new QueueMonitor;

    $status = $monitor->statusFromData('database', 'mail', 10, 2, 45);

    expect($status->connection)->toBe('database')
        ->and($status->queue)->toBe('mail')
        ->and($status->depth)->toBe(10)
        ->and($status->failedCount)->toBe(2)
        ->and($status->oldestJobAgeSeconds)->toBe(45);
});
