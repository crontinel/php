<?php

declare(strict_types=1);

namespace Crontinel\Cron;

use Cron\CronExpression;

final class CronExpressionHelper
{
    public static function nextDue(string $expression): ?\DateTimeImmutable
    {
        try {
            $expr = new CronExpression($expression);

            return \DateTimeImmutable::createFromMutable($expr->getNextRunDate());
        } catch (\Throwable) {
            return null;
        }
    }

    public static function previousDue(string $expression): ?\DateTimeImmutable
    {
        try {
            $expr = new CronExpression($expression);

            return \DateTimeImmutable::createFromMutable($expr->getPreviousRunDate());
        } catch (\Throwable) {
            return null;
        }
    }

    public static function isLate(
        \DateTimeInterface $lastRunAt,
        string $expression,
        int $graceSeconds = 120,
    ): bool {
        $previousDue = self::previousDue($expression);

        if ($previousDue === null) {
            return false;
        }

        // Not late if the last run happened after the previous scheduled time
        if ($lastRunAt >= $previousDue) {
            return false;
        }

        $now = new \DateTimeImmutable;
        $secondsSinceDue = $now->getTimestamp() - $previousDue->getTimestamp();

        return $secondsSinceDue > $graceSeconds;
    }
}
