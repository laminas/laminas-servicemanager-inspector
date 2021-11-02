<?php

declare(strict_types=1);

namespace LaminasTest\ServiceManager\Inspector\Event;

use Laminas\ServiceManager\Inspector\Event\CircularDependencyDetectedEvent;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Laminas\ServiceManager\Inspector\Event\CircularDependencyDetectedEvent
 */
class CircularDependencyDetectedEventTest extends TestCase
{
    public function testReturnsDependencyNameThatWasProvided()
    {
        $event = new CircularDependencyDetectedEvent('a', []);

        $this->assertSame('a', $event->getDependencyName());
    }

    public function testReturnsProperError()
    {
        $event = new CircularDependencyDetectedEvent('a', ['c', 'b']);

        $this->assertSame('Circular dependency detected: c -> b -> a', $event->getError());
    }
}
