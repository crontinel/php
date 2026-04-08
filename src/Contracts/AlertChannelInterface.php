<?php

declare(strict_types=1);

namespace Crontinel\Contracts;

use Crontinel\Data\AlertEvent;

interface AlertChannelInterface
{
    public function send(AlertEvent $event): void;
}
