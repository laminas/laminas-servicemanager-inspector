<?php

declare(strict_types=1);

namespace LaminasTest\ServiceManager\Inspector\Event;

use Laminas\ServiceManager\Inspector\Event\CustomFactoryEnteredEvent;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Laminas\ServiceManager\Inspector\Event\CustomFactoryEnteredEvent
 */
class CustomFactoryEnteredEventTest extends TestCase
{
    public function testReturnsDependencyNameThatWasProvided()
    {
        $event = new CustomFactoryEnteredEvent('a', []);

        $this->assertSame('a', $event->getDependencyName());
    }

    public function testReturnsInstantiationStackThatWasProvided()
    {
        $event = new CustomFactoryEnteredEvent('', ['a']);

        $this->assertSame(['a'], $event->getInstantiationStack());
    }
}
