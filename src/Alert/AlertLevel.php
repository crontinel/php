<?php

declare(strict_types=1);

namespace Crontinel\Alert;

enum AlertLevel: string
{
    case CRITICAL = 'critical';
    case WARNING = 'warning';
    case INFO = 'info';
    case RESOLVED = 'resolved';

    public function isValid(): bool
    {
        return true; // only valid values possible via enum
    }
}
