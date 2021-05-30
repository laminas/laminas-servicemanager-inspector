<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\ServiceManager\Inspector\Event;

use Laminas\ServiceManager\Inspector\Event\MissingFactoryDetectedEvent;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Laminas\ServiceManager\Inspector\Event\MissingFactoryDetectedEvent
 */
class MissingFactoryDetectedEventTest extends TestCase
{
    public function testReturnsDependencyNameThatWasProvided()
    {
        $event = new MissingFactoryDetectedEvent('a');

        $this->assertSame('a', $event->getDependencyName());
    }

    public function testReturnsProperError()
    {
        $event = new MissingFactoryDetectedEvent('a');

        $this->assertSame("No factory is provided for 'a' service", $event->getError());
    }
}
