<?php

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\EventReporter;

use Laminas\ServiceManager\Inspector\Event\EventInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface EventReporterInterface
{
    /**
     * @psalm-param list<EventInterface>           $events
     * @param EventInterface[] $events
     */
    public function __invoke(array $events, OutputInterface $output): void;
}
