<?php

declare(strict_types=1);

namespace Crontinel\Contracts;

interface MonitorInterface
{
    /**
     * Returns true when the monitored resource is healthy.
     */
    public function isHealthy(): bool;
}
