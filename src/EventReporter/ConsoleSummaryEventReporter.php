<?php

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\EventReporter;

use Laminas\ServiceManager\Inspector\Event\AutowireFactoryEnteredEvent;
use Laminas\ServiceManager\Inspector\Event\CustomFactoryEnteredEvent;
use Laminas\ServiceManager\Inspector\Event\EnterEventInterface;
use Laminas\ServiceManager\Inspector\Event\EventInterface;
use Laminas\ServiceManager\Inspector\Event\InvokableEnteredEvent;
use Laminas\ServiceManager\Inspector\Event\TerminalEventInterface;
use Laminas\ServiceManager\Inspector\EventReporter\ConsoleColor\ConsoleColorInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function count;
use function sprintf;

class ConsoleSummaryEventReporter implements EventReporterInterface
{
    private ConsoleColorInterface $consoleColor;

    public function __construct(ConsoleColorInterface $consoleColor)
    {
        $this->consoleColor = $consoleColor;
    }

    /**
     * @psalm-param list<EventInterface> $events
     * @param EventInterface[] $events
     */
    public function __invoke(array $events, OutputInterface $output): void
    {
        $this->printSummary($events, $output);
    }

    /**
     * @psalm-param list<EventInterface> $events
     * @param EventInterface[] $events
     */
    private function printSummary(array $events, OutputInterface $output): void
    {
        $totalFactoriesCount = $this->countEnterEvent(
            $events,
            [
                InvokableEnteredEvent::class,
                AutowireFactoryEnteredEvent::class,
                CustomFactoryEnteredEvent::class,
            ]
        );
        $output->write(
            sprintf(
                "\nTotal factories found: %s 🏭\n",
                $this->consoleColor->success((string) $totalFactoriesCount),
            )
        );

        $customFactoriesCount = $this->countEnterEvent($events, [CustomFactoryEnteredEvent::class]);
        $output->write(
            sprintf(
                "Custom factories skipped: %s 🛠️\n",
                $this->consoleColor->success((string) $customFactoriesCount)
            )
        );

        $autowireFactoriesCount = $this->countEnterEvent($events, [AutowireFactoryEnteredEvent::class]);
        $output->write(
            sprintf(
                "Autowire factories analyzed: %s 🔥\n",
                $this->consoleColor->success(
                    (string) $autowireFactoriesCount
                ),
            )
        );

        $invokableCount = $this->countEnterEvent($events, [InvokableEnteredEvent::class]);
        $output->write(
            sprintf(
                "Invokables analyzed: %s 📦\n",
                $this->consoleColor->success(
                    (string) $invokableCount
                ),
            )
        );

        $maxDeep = $this->consoleColor->success((string) $this->countMaxInstantiationDeep($events));
        $output->write(sprintf("Maximum instantiation deep: %s 🏊\n", $maxDeep));

        $conclusion          = $this->consoleColor->success(sprintf("\nAs far as I can tell, it's all good 🚀\n"));
        $terminalEventsCount = $this->countTerminalEvents($events);
        if ($terminalEventsCount > 0) {
            $errorCounter = $this->consoleColor->error((string) $terminalEventsCount);
            $conclusion   = sprintf("\nTotal errors found: %s 😕\n", $errorCounter);
        }

        $output->write($conclusion);
    }

    /**
     * @psalm-param list<EventInterface> $events
     * @psalm-param list<class-string> $desiredEvents
     * @param EventInterface[] $events
     */
    private function countEnterEvent(array $events, array $desiredEvents): int
    {
        $foundEventCount = 0;
        foreach ($events as $event) {
            if ($event instanceof TerminalEventInterface) {
                continue;
            }

            foreach ($desiredEvents as $desiredEvent) {
                if ($event instanceof $desiredEvent) {
                    $foundEventCount++;
                }
            }
        }

        return $foundEventCount;
    }

    /**
     * @psalm-param list<EventInterface> $events
     * @param EventInterface[] $events
     */
    private function countMaxInstantiationDeep(array $events): int
    {
        $maxInstantiationDeep = 0;
        foreach ($events as $event) {
            if ($event instanceof EnterEventInterface) {
                $deep = count($event->getInstantiationStack());
                if ($deep > $maxInstantiationDeep) {
                    $maxInstantiationDeep = $deep;
                }
            }
        }

        return $maxInstantiationDeep;
    }

    /**
     * @psalm-param list<EventInterface> $events
     * @param EventInterface[] $events
     */
    private function countTerminalEvents(array $events): int
    {
        $count = 0;
        foreach ($events as $event) {
            if ($event instanceof TerminalEventInterface) {
                $count++;
            }
        }

        return $count;
    }
}
