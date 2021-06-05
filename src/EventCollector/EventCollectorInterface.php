<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

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
