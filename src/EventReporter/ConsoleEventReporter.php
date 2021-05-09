<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

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
use function str_repeat;

final class ConsoleEventReporter implements EventReporterInterface
{
    /** @var ConsoleColorInterface */
    private $consoleColor;

    public function __construct(ConsoleColorInterface $consoleColor)
    {
        $this->consoleColor = $consoleColor;
    }

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

        $this->printSummary($events, $output);
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

    /**
     * @psalm-var list<EventInterface> $events
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

        $conclusion          = $this->consoleColor->success(
            sprintf(
                "\nAs far as I can tell, it's all good 🚀\n",
            )
        );
        $terminalEventsCount = $this->countTerminalEvents($events);
        if ($terminalEventsCount > 0) {
            $errorCounter = $this->consoleColor->error((string) $terminalEventsCount);
            $conclusion   = sprintf("\nTotal errors found: %s 😕\n", $errorCounter);
        }

        $output->write($conclusion);
    }

    /**
     * @psalm-var list<EventInterface> $events
     * @psalm-var list<class-string> $desiredEvents
     * @var EventInterface[] $events
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
