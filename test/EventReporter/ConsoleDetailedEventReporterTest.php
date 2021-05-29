<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\ServiceManager\Inspector\EventReporter;

use Laminas\ServiceManager\Inspector\Event\CustomFactoryEnteredEvent;
use Laminas\ServiceManager\Inspector\Event\UnexpectedScalarDetectedEvent;
use Laminas\ServiceManager\Inspector\EventReporter\ConsoleColor\NullConsoleColor;
use Laminas\ServiceManager\Inspector\EventReporter\ConsoleDetailedEventReporter;
use Laminas\ServiceManager\Inspector\EventReporter\EventReporterInterface;
use Laminas\ServiceManager\Inspector\EventReporter\NullEventReporter;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @covers \Laminas\ServiceManager\Inspector\EventReporter\ConsoleDetailedEventReporter
 */
class ConsoleDetailedEventReporterTest extends TestCase
{
    use ProphecyTrait;

    public function testPrintEnterAndTerminalEvents()
    {
        $events = [
            new CustomFactoryEnteredEvent('dep1', ['a', 'b']),
            new UnexpectedScalarDetectedEvent('dep2', 'param'),
        ];
        $buffer = new BufferedOutput();

        $reporter = new ConsoleDetailedEventReporter(new NullConsoleColor(), new NullEventReporter());
        $reporter($events, $buffer);

        $this->assertSame(
            "    └─dep1\n\n\n  ReflectionBasedAbstractFactory cannot resolve scalar 'param' for 'dep2' service.\n\n\n",
            $buffer->fetch()
        );
    }

    public function testCallsSummaryEventReporter()
    {
        $summaryEventReporter = $this->prophesize(EventReporterInterface::class);
        $summaryEventReporter->__invoke(
            Argument::type('array'),
            Argument::type(OutputInterface::class)
        )->shouldBeCalled();

        $reporter = new ConsoleDetailedEventReporter(new NullConsoleColor(), $summaryEventReporter->reveal());
        $reporter([], new NullOutput());
    }
}
