<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\EventReporter;

use Laminas\ServiceManager\Inspector\Event\EventInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class NullEventReporter implements EventReporterInterface
{
    /**
     * @psalm-param list<EventInterface> $events
     * @param EventInterface[] $events
     */
    public function __invoke(array $events, OutputInterface $output): void
    {
    }
}
