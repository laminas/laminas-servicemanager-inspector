<?php

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\EventCollector;

use Laminas\ServiceManager\Inspector\Event\EventInterface;

final class NullEventCollector implements EventCollectorInterface
{
    public function __invoke(EventInterface $event): void
    {
    }

    /**
     * @psalm-return list<EventInterface>
     * @return EventInterface[]
     */
    public function release(): array
    {
        return [];
    }

    public function hasTerminalEvent(): bool
    {
        return false;
    }
}
