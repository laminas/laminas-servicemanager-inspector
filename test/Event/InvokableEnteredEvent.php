<?php

declare(strict_types=1);

namespace LaminasTest\ServiceManager\Inspector\Event;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Laminas\ServiceManager\Inspector\Event\InvokableEnteredEvent
 */
class InvokableEnteredEvent extends TestCase
{
    public function testReturnsDependencyNameThatWasProvided()
    {
        $event = new InvokableEnteredEvent('a', []);

        $this->assertSame('a', $event->getDependencyName());
    }

    public function testReturnsInstantiationStackThatWasProvided()
    {
        $event = new InvokableEnteredEvent('', ['a']);

        $this->assertSame(['a'], $event->getInstantiationStack());
    }
}
