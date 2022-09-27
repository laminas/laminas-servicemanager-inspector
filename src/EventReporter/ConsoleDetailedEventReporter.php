<?php

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\EventReporter;

use Laminas\ServiceManager\Inspector\Event\EnterEventInterface;
use Laminas\ServiceManager\Inspector\Event\EventInterface;
use Laminas\ServiceManager\Inspector\Event\TerminalEventInterface;
use Laminas\ServiceManager\Inspector\EventReporter\ConsoleColor\ConsoleColorInterface;
use Laminas\ServiceManager\Inspector\EventReporter\ConsoleSummaryEventReporter;
use Symfony\Component\Console\Output\OutputInterface;

use function count;
use function sprintf;
use function str_repeat;

final class ConsoleDetailedEventReporter implements EventReporterInterface
{
    private ConsoleColorInterface $consoleColor;

    private ConsoleSummaryEventReporter $summaryReporter;

    public function __construct(ConsoleColorInterface $consoleColor, ConsoleSummaryEventReporter $summaryReporter)
    {
        $this->consoleColor    = $consoleColor;
        $this->summaryReporter = $summaryReporter;
    }

    /**
     * @psalm-param list<EventInterface> $events
     * @param EventInterface[] $events
     */
    public function __invoke(array $events, OutputInterface $output): void
    {
        foreach ($events as $event) {
            if ($event instanceof EnterEventInterface) {
                $this->printEnterEvent($event, $output);
            }
        }

        foreach ($events as $event) {
            if ($event instanceof TerminalEventInterface) {
                $this->printTerminalEvent($event, $output);
            }
        }

        ($this->summaryReporter)($events, $output);
    }

    private function printTerminalEvent(TerminalEventInterface $event, OutputInterface $output): void
    {
        $output->write(sprintf("%s\n\n", $this->consoleColor->critical("\n\n  " . $event->getError() . "\n")));
    }

    private function printEnterEvent(EnterEventInterface $event, OutputInterface $output): void
    {
        $text = $this->consoleColor->normal($event->getDependencyName());
        if (count($event->getInstantiationStack()) === 0) {
            $text = $this->consoleColor->warning($event->getDependencyName());
        }

        $output->write(sprintf(str_repeat('  ', count($event->getInstantiationStack()))));
        $output->write(sprintf("└─%s\n", $text));
    }
}
