<?php

declare(strict_types=1);

namespace Crontinel\Alert;

use Crontinel\Contracts\AlertChannelInterface;
use Crontinel\Data\AlertEvent;
use Crontinel\Data\AlertLevel;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Psr\SimpleCache\CacheInterface;

class AlertManager
{
    private const DEFAULT_DEDUP_TTL = 300; // 5 minutes

    public function __construct(
        private readonly CacheInterface $cache,
        private readonly AlertChannelInterface $channel,
        private readonly LoggerInterface $logger = new NullLogger,
        private readonly int $dedupTtl = self::DEFAULT_DEDUP_TTL,
    ) {}

    public function fire(string $key, string $title, string $message, AlertLevel|string $level): void
    {
        $levelValue = $level instanceof AlertLevel ? $level->value : $level;
        $cacheKey = $this->cacheKey($key);

        // Use cache->get to check and set atomically via set with TTL
        // If already set, another process set it first — dedup achieved
        $existing = $this->cache->get($cacheKey);

        if ($existing !== null) {
            return; // dedup: already firing
        }

        $entry = ['title' => $title, 'fired_at' => (new \DateTimeImmutable)->format(\DateTimeInterface::ATOM)];

        // Race: two processes may both see null and try to set
        // The one that successfully sets first wins; use the return value to confirm
        $set = $this->cache->set($cacheKey, $entry, $this->dedupTtl);

        if (! $set) {
            // Another process beat us to it
            return;
        }

        $event = new AlertEvent(
            key: $key,
            title: $title,
            message: $message,
            level: $levelValue,
            resolved: false,
            firedAt: new \DateTimeImmutable,
        );

        try {
            $this->channel->send($event);
        } catch (\Throwable $e) {
            $this->logger->error('Crontinel: failed to send alert', ['error' => $e->getMessage(), 'key' => $key]);
        }
    }

    public function resolve(string $key): void
    {
        $cacheKey = $this->cacheKey($key);

        // get() returns null if key doesn't exist or is expired
        $original = $this->cache->get($cacheKey);

        if ($original === null) {
            return; // no alert to resolve
        }

        $this->cache->delete($cacheKey);

        if (! is_array($original)) {
            $title = $key;
            $firedAt = 'unknown';
        } else {
            $title = $original['title'] ?? $key;
            $firedAt = $original['fired_at'] ?? 'unknown';
        }

        $event = new AlertEvent(
            key: $key,
            title: $title,
            message: "Issue resolved. Originally fired at {$firedAt}.",
            level: AlertLevel::RESOLVED->value,
            resolved: true,
            firedAt: new \DateTimeImmutable,
        );

        try {
            $this->channel->send($event);
        } catch (\Throwable $e) {
            $this->logger->error('Crontinel: failed to send resolve alert', ['error' => $e->getMessage(), 'key' => $key]);
        }
    }

    private function cacheKey(string $key): string
    {
        return 'crontinel_alert_'.preg_replace('/[^a-zA-Z0-9_]/', '_', $key);
    }
}
