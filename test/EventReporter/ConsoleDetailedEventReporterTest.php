<?php

declare(strict_types=1);

namespace LaminasTest\ServiceManager\Inspector\EventReporter;

use Laminas\ServiceManager\Inspector\Event\CustomFactoryEnteredEvent;
use Laminas\ServiceManager\Inspector\Event\UnresolvableParameterDetectedEvent;
use Laminas\ServiceManager\Inspector\EventReporter\ConsoleColor\NullConsoleColor;
use Laminas\ServiceManager\Inspector\EventReporter\ConsoleDetailedEventReporter;
use Laminas\ServiceManager\Inspector\EventReporter\ConsoleSummaryEventReporter;
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
            new UnresolvableParameterDetectedEvent('dep2', 'p'),
        ];
        $buffer = new BufferedOutput();

        $summaryEventReporter = $this->prophesize(ConsoleSummaryEventReporter::class);

        $reporter = new ConsoleDetailedEventReporter(new NullConsoleColor(), $summaryEventReporter->reveal());
        $reporter($events, $buffer);

        $this->assertSame(
            "    └─dep1\n\n\n  ReflectionBasedAbstractFactory cannot resolve parameter 'p' of 'dep2' service.\n\n\n",
            $buffer->fetch()
        );
    }

    public function testCallsSummaryEventReporter()
    {
        $summaryEventReporter = $this->prophesize(ConsoleSummaryEventReporter::class);
        $summaryEventReporter->__invoke(
            Argument::type('array'),
            Argument::type(OutputInterface::class)
        )->shouldBeCalled();

        $reporter = new ConsoleDetailedEventReporter(new NullConsoleColor(), $summaryEventReporter->reveal());
        $reporter([], new NullOutput());
    }
}
