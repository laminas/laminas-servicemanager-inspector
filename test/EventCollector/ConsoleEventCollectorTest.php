<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\ServiceManager\Inspector\EventCollector;

use Laminas\ServiceManager\Inspector\Event\CustomFactoryEnteredEvent;
use Laminas\ServiceManager\Inspector\Event\MissingFactoryDetectedEvent;
use Laminas\ServiceManager\Inspector\EventCollector\ConsoleEventCollector;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\NullOutput;

/**
 * @covers \Laminas\ServiceManager\Inspector\EventCollector\ConsoleEventCollector
 */
class ConsoleEventCollectorTest extends TestCase
{
    public function testReturnsErrorExitCodeReturnedWhenTerminalEventIsProvided()
    {
        $collector = new ConsoleEventCollector();
        $collector->collect(new MissingFactoryDetectedEvent('a'));

        $exitCode = $collector->release(new NullOutput());

        $this->assertSame(1, $exitCode);
    }

    public function testReturnsSuccessExitCodeReturnedWhenNoTerminalEventIsProvided()
    {
        $collector = new ConsoleEventCollector();
        $collector->collect(new CustomFactoryEnteredEvent('a', []));

        $exitCode = $collector->release(new NullOutput());

        $this->assertSame(0, $exitCode);
    }
}
