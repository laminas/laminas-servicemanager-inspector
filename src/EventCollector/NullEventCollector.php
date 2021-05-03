<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\EventCollector;

use Laminas\ServiceManager\Inspector\Event\EventInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class NullEventCollector implements EventCollectorInterface
{
    public function collect(EventInterface $event): void
    {
    }

    public function release(OutputInterface $output): int
    {
        return 0;
    }
}
