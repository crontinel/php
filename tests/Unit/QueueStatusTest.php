<?php

declare(strict_types=1);

use Crontinel\Data\QueueStatus;

it('is healthy when within all thresholds', function (): void {
    $status = new QueueStatus('redis', 'default', 10, 0, 60);
    expect($status->isHealthy())->toBeTrue();
});

it('is not healthy when depth exceeds threshold', function (): void {
    $status = new QueueStatus('redis', 'default', 1001, 0, null, depthThreshold: 1000);
    expect($status->isHealthy())->toBeFalse();
});

it('is not healthy when oldest job age exceeds threshold', function (): void {
    $status = new QueueStatus('redis', 'default', 0, 0, 400, waitTimeThresholdSeconds: 300);
    expect($status->isHealthy())->toBeFalse();
});

it('is healthy when oldest job age is null', function (): void {
    $status = new QueueStatus('redis', 'default', 0, 0, null);
    expect($status->isHealthy())->toBeTrue();
});
