<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\ServiceManager\Inspector\EventCollector;

use Laminas\ServiceManager\Inspector\Event\TerminalEventInterface;
use Laminas\ServiceManager\Inspector\EventCollector\NullEventCollector;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Console\Output\NullOutput;

/**
 * @covers \Laminas\ServiceManager\Inspector\EventCollector\NullEventCollector
 */
class NullEventCollectorTest extends TestCase
{
    use ProphecyTrait;

    public function testReturnsUnsuccessfulReturnCodeOnTerminalEvent()
    {
        $collector = new NullEventCollector();
        $collector->collect($this->prophesize(TerminalEventInterface::class)->reveal());

        $this->assertSame(1, $collector->release(new NullOutput()));
    }
}
