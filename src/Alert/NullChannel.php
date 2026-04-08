<?php

declare(strict_types=1);

namespace Crontinel\Alert;

use Crontinel\Contracts\AlertChannelInterface;
use Crontinel\Data\AlertEvent;

final class NullChannel implements AlertChannelInterface
{
    public function send(AlertEvent $event): void
    {
        // no-op — used in testing or when alerts are disabled
    }
}
