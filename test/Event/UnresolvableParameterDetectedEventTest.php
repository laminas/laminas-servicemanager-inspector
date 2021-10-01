<?php

declare(strict_types=1);

namespace LaminasTest\ServiceManager\Inspector\Event;

use Laminas\ServiceManager\Inspector\Event\UnresolvableParameterDetectedEvent;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Laminas\ServiceManager\Inspector\Event\UnresolvableParameterDetectedEvent
 */
class UnresolvableParameterDetectedEventTest extends TestCase
{
    public function testReturnsDependencyNameThatWasProvided()
    {
        $event = new UnresolvableParameterDetectedEvent('a', '');

        $this->assertSame('a', $event->getDependencyName());
    }

    public function testReturnsProperError()
    {
        $event = new UnresolvableParameterDetectedEvent('a', 'b');

        $this->assertSame(
            "ReflectionBasedAbstractFactory cannot resolve parameter 'b' of 'a' service.",
            $event->getError()
        );
    }
}
