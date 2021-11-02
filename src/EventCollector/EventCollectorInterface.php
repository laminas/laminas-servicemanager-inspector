<?php

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\EventCollector;

use Laminas\ServiceManager\Inspector\Event\EventInterface;

interface EventCollectorInterface
{
    public function __invoke(EventInterface $event): void;

    /**
     * @psalm-return list<EventInterface>
     * @return EventInterface[]
     */
    public function release(): array;

    public function hasTerminalEvent(): bool;
}
