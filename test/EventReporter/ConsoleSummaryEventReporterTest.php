<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\ServiceManager\Inspector\EventReporter;

use Laminas\ServiceManager\Inspector\Event\AutowireFactoryEnteredEvent;
use Laminas\ServiceManager\Inspector\Event\CustomFactoryEnteredEvent;
use Laminas\ServiceManager\Inspector\Event\InvokableEnteredEvent;
use Laminas\ServiceManager\Inspector\Event\UnexpectedScalarDetectedEvent;
use Laminas\ServiceManager\Inspector\EventReporter\ConsoleColor\NullConsoleColor;
use Laminas\ServiceManager\Inspector\EventReporter\ConsoleSummaryEventReporter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @covers \Laminas\ServiceManager\Inspector\EventReporter\ConsoleSummaryEventReporter
 */
class ConsoleSummaryEventReporterTest extends TestCase
{
    public function testPrintsPositiveReportWhenNoTerminalEventsAreProvided()
    {
        $events = [
            new CustomFactoryEnteredEvent('a', ['b', 'c']),
            new InvokableEnteredEvent('b', ['b']),
            new AutowireFactoryEnteredEvent('c', []),
        ];

        $reporter = new ConsoleSummaryEventReporter(new NullConsoleColor());
        $buffer   = new BufferedOutput();
        $reporter($events, $buffer);

        $this->assertSame(
            '
Total factories found: 3 🏭
Custom factories skipped: 1 🛠️
Autowire factories analyzed: 1 🔥
Invokables analyzed: 1 📦
Maximum instantiation deep: 2 🏊

As far as I can tell, it\'s all good 🚀
',
            $buffer->fetch()
        );
    }

    public function testPrintsNegativeReportWhenTerminalEventsAreProvided()
    {
        $events = [
            new CustomFactoryEnteredEvent('a', ['b', 'c']),
            new InvokableEnteredEvent('b', ['b']),
            new AutowireFactoryEnteredEvent('c', []),
            new UnexpectedScalarDetectedEvent('d', 'param'),
        ];

        $reporter = new ConsoleSummaryEventReporter(new NullConsoleColor());
        $buffer   = new BufferedOutput();
        $reporter($events, $buffer);

        $this->assertSame(
            '
Total factories found: 3 🏭
Custom factories skipped: 1 🛠️
Autowire factories analyzed: 1 🔥
Invokables analyzed: 1 📦
Maximum instantiation deep: 2 🏊

Total errors found: 1 😕
',
            $buffer->fetch()
        );
    }
}
