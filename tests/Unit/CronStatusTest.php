<?php

declare(strict_types=1);

use Crontinel\Data\CronStatus;

it('is healthy when status is ok', function (): void {
    $status = new CronStatus('cmd', '* * * * *', 'ok', null, null, null, null);
    expect($status->isHealthy())->toBeTrue();
});

it('is not healthy when status is failed', function (): void {
    $status = new CronStatus('cmd', '* * * * *', 'failed', null, 1, null, null);
    expect($status->isHealthy())->toBeFalse();
});

it('is not healthy when status is late', function (): void {
    $status = new CronStatus('cmd', '* * * * *', 'late', null, 0, null, null);
    expect($status->isHealthy())->toBeFalse();
});

it('is not healthy when status is never_run', function (): void {
    $status = new CronStatus('cmd', '* * * * *', 'never_run', null, null, null, null);
    expect($status->isHealthy())->toBeFalse();
});
