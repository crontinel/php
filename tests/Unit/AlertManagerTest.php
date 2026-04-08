<?php

declare(strict_types=1);

use Crontinel\Alert\AlertManager;
use Crontinel\Alert\NullChannel;
use Crontinel\Contracts\AlertChannelInterface;
use Crontinel\Data\AlertEvent;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Psr16Cache;

function makeCache(): Psr16Cache
{
    return new Psr16Cache(new ArrayAdapter);
}

it('fires an alert via the channel', function (): void {
    $fired = [];
    $channel = new class($fired) implements AlertChannelInterface
    {
        public function __construct(private array &$fired) {}

        public function send(AlertEvent $event): void
        {
            $this->fired[] = $event;
        }
    };

    $manager = new AlertManager(makeCache(), $channel);
    $manager->fire('test', 'Title', 'Message', 'warning');

    expect($fired)->toHaveCount(1);
    expect($fired[0]->key)->toBe('test');
    expect($fired[0]->level)->toBe('warning');
    expect($fired[0]->resolved)->toBeFalse();
});

it('does not fire the same alert twice within the dedup window', function (): void {
    $count = 0;
    $channel = new class($count) implements AlertChannelInterface
    {
        public function __construct(private int &$count) {}

        public function send(AlertEvent $event): void
        {
            $this->count++;
        }
    };

    $manager = new AlertManager(makeCache(), $channel);
    $manager->fire('test', 'Title', 'Message', 'warning');
    $manager->fire('test', 'Title', 'Message', 'warning');

    expect($count)->toBe(1);
});

it('sends a resolved event when resolving an open alert', function (): void {
    $events = [];
    $channel = new class($events) implements AlertChannelInterface
    {
        public function __construct(private array &$events) {}

        public function send(AlertEvent $event): void
        {
            $this->events[] = $event;
        }
    };

    $manager = new AlertManager(makeCache(), $channel);
    $manager->fire('test', 'Title', 'Message', 'critical');
    $manager->resolve('test');

    expect($events)->toHaveCount(2);
    expect($events[1]->resolved)->toBeTrue();
    expect($events[1]->level)->toBe('resolved');
});

it('does nothing when resolving an alert that was never fired', function (): void {
    $count = 0;
    $channel = new class($count) implements AlertChannelInterface
    {
        public function __construct(private int &$count) {}

        public function send(AlertEvent $event): void
        {
            $this->count++;
        }
    };

    $manager = new AlertManager(makeCache(), $channel);
    $manager->resolve('nonexistent');

    expect($count)->toBe(0);
});

it('null channel is a no-op', function (): void {
    $manager = new AlertManager(makeCache(), new NullChannel);
    $manager->fire('test', 'Title', 'Message', 'critical');
    // no exception = pass
    expect(true)->toBeTrue();
});
