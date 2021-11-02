<?php

declare(strict_types=1);

namespace LaminasTest\ServiceManager\Inspector\Event;

use Laminas\ServiceManager\Inspector\Event\AutowireFactoryEnteredEvent;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Laminas\ServiceManager\Inspector\Event\AutowireFactoryEnteredEvent
 */
class AutowireFactoryEnteredEventTest extends TestCase
{
    public function testReturnsDependencyNameThatWasProvided()
    {
        $event = new AutowireFactoryEnteredEvent('a', []);

        $this->assertSame('a', $event->getDependencyName());
    }

    public function testReturnsInstantiationStackThatWasProvided()
    {
        $event = new AutowireFactoryEnteredEvent('', ['a']);

        $this->assertSame(['a'], $event->getInstantiationStack());
    }
}
