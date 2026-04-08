<?php

declare(strict_types=1);

namespace Crontinel\Alert;

use Crontinel\Contracts\AlertChannelInterface;
use Crontinel\Data\AlertEvent;
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

    public function fire(string $key, string $title, string $message, string $level): void
    {
        $cacheKey = $this->cacheKey($key);

        if ($this->cache->has($cacheKey)) {
            return;
        }

        $entry = ['title' => $title, 'fired_at' => (new \DateTimeImmutable)->format(\DateTimeInterface::ISO8601)];

        $this->cache->set($cacheKey, $entry, $this->dedupTtl);

        $event = new AlertEvent(
            key: $key,
            title: $title,
            message: $message,
            level: $level,
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

        if (! $this->cache->has($cacheKey)) {
            return;
        }

        $original = $this->cache->get($cacheKey);
        $this->cache->delete($cacheKey);

        $event = new AlertEvent(
            key: $key,
            title: $original['title'] ?? $key,
            message: "Issue resolved. Originally fired at {$original['fired_at']}.",
            level: 'resolved',
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
