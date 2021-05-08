<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\EventCollector;

use Laminas\ServiceManager\Inspector\Event\EventInterface;
use Laminas\ServiceManager\Inspector\Event\TerminalEventInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class NullEventCollector implements EventCollectorInterface
{
    /** @var int */
    private $returnCode = 0;

    public function collect(EventInterface $event): void
    {
        if ($event instanceof TerminalEventInterface) {
            $this->returnCode = 1;
        }
    }

    public function release(OutputInterface $output): int
    {
        return $this->returnCode;
    }
}
