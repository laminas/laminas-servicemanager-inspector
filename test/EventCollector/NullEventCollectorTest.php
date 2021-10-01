<?php

declare(strict_types=1);

namespace LaminasTest\ServiceManager\Inspector\EventCollector;

use Laminas\ServiceManager\Inspector\Event\TerminalEventInterface;
use Laminas\ServiceManager\Inspector\EventCollector\NullEventCollector;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers \Laminas\ServiceManager\Inspector\EventCollector\NullEventCollector
 */
class NullEventCollectorTest extends TestCase
{
    use ProphecyTrait;

    public function testHasTerminalEventsReturnsFalseOnNoEventsProvided()
    {
        $collector = new NullEventCollector();

        $this->assertFalse($collector->hasTerminalEvent());
    }

    public function testHasTerminalEventsReturnsFalseOnTerminalEventProvided()
    {
        $collector = new NullEventCollector();
        $collector($this->prophesize(TerminalEventInterface::class)->reveal());

        $this->assertFalse($collector->hasTerminalEvent());
    }
}
