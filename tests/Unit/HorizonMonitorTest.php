<?php

declare(strict_types=1);

use Crontinel\Monitors\HorizonMonitor;

it('returns running status when master is running', function () {
    $monitor = new HorizonMonitor(['failed_jobs_per_minute_threshold' => 5.0]);

    $status = $monitor->statusFromData('running', [], 0.0);

    expect($status->running)->toBeTrue()
        ->and($status->isHealthy())->toBeTrue();
});

it('returns stopped status when master is stopped', function () {
    $monitor = new HorizonMonitor;

    $status = $monitor->statusFromData('stopped', [], 0.0);

    expect($status->running)->toBeFalse()
        ->and($status->isHealthy())->toBeFalse();
});

it('returns paused status when master is paused', function () {
    $monitor = new HorizonMonitor;

    $status = $monitor->statusFromData('paused', [], 0.0);

    expect($status->pausedAt)->not->toBeNull()
        ->and($status->isHealthy())->toBeFalse();
});

it('is unhealthy when failed jobs per minute exceeds threshold', function () {
    $monitor = new HorizonMonitor(['failed_jobs_per_minute_threshold' => 5.0]);

    $status = $monitor->statusFromData('running', [], 10.0);

    expect($status->isHealthy())->toBeFalse();
});
