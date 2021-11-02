<?php

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\EventCollector;

use Laminas\ServiceManager\Inspector\Event\EventInterface;
use Laminas\ServiceManager\Inspector\Event\TerminalEventInterface;

final class EventCollector implements EventCollectorInterface
{
    /**
     * @psalm-var list<EventInterface>
     * @var EventInterface[]
     */
    private $events = [];

    /**
     * TODO preserve number of occurred events per dependency
     * TODO preserve the events with the longest instantiation deep only
     */
    public function __invoke(EventInterface $event): void
    {
        foreach ($this->events as $existingEvent) {
            if ($existingEvent->getDependencyName() === $event->getDependencyName()) {
                return;
            }
        }

        $this->events[] = $event;
    }

    /**
     * @psalm-return list<EventInterface>
     * @return EventInterface[]
     */
    public function release(): array
    {
        $events = $this->events;

        $this->events = [];

        return $events;
    }

    public function hasTerminalEvent(): bool
    {
        foreach ($this->events as $event) {
            if ($event instanceof TerminalEventInterface) {
                return true;
            }
        }

        return false;
    }
}
