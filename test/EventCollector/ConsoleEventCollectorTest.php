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
use Laminas\ServiceManager\Inspector\EventCollector\EventCollector;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Laminas\ServiceManager\Inspector\EventCollector\EventCollector
 */
class ConsoleEventCollectorTest extends TestCase
{
    public function testReturnsErrorExitCodeReturnedWhenTerminalEventIsProvided()
    {
        $collector = new EventCollector();
        $collector(new MissingFactoryDetectedEvent('a'));

        $this->assertTrue($collector->hasTerminalEvent());
    }

    public function testReturnsSuccessExitCodeReturnedWhenNoTerminalEventIsProvided()
    {
        $collector = new EventCollector();
        $collector(new CustomFactoryEnteredEvent('a', []));

        $this->assertfalse($collector->hasTerminalEvent());
    }
}
