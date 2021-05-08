<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\ServiceManager\Inspector\Event;

use Laminas\ServiceManager\Inspector\Event\AutoloadProblemDetectedEvent;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Laminas\ServiceManager\Inspector\Event\AutoloadProblemDetectedEvent
 */
class AutoloadProblemDetectedEventTest extends TestCase
{
    public function testReturnsDependencyNameThatWasProvided()
    {
        $event = new AutoloadProblemDetectedEvent('a', 'b');

        $this->assertSame('a', $event->getDependencyName());
    }

    public function testReturnsProperError()
    {
        $event = new AutoloadProblemDetectedEvent('dependency', 'factory');

        $this->assertSame("Cannot autoload factory class 'factory' for service 'dependency'", $event->getError());
    }
}