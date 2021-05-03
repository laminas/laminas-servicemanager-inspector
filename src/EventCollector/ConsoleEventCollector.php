<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\EventCollector;

use Laminas\ServiceManager\Inspector\Event\AutowireFactoryEnteredEvent;
use Laminas\ServiceManager\Inspector\Event\CustomFactoryEnteredEvent;
use Laminas\ServiceManager\Inspector\Event\EnterEvent;
use Laminas\ServiceManager\Inspector\Event\EventInterface;
use Laminas\ServiceManager\Inspector\Event\InvokableEnteredEvent;
use Laminas\ServiceManager\Inspector\Event\TerminalEventInterface;
use Symfony\Component\Console\Color;
use Symfony\Component\Console\Output\OutputInterface;
use function array_filter;
use function count;
use function in_array;
use function sprintf;
use function str_repeat;

final class ConsoleEventCollector implements EventCollectorInterface
{
    /** @var EventInterface[] */
    private $events;

    /** @var Color */
    private $rootDependencyColor;

    /** @var Color */
    private $dependencyColor;

    /** @var Color */
    private $errorColor;

    /** @var Color */
    private $successfulResultColor;

    /** @var Color */
    private $failedResultColor;

    public function collect(EventInterface $event): void
    {
        $this->events[] = $event;
        $this->rootDependencyColor = new Color('yellow');
        $this->dependencyColor = new Color('white');
        $this->errorColor = new Color('white', 'red');
        $this->successfulResultColor = new Color('green');
        $this->failedResultColor = new Color('red');
    }

    public function release(OutputInterface $output): int
    {
        foreach ($this->events as $event) {
            if ($event instanceof EnterEvent) {
                $this->printEnterEvent($event, $output);
            }
            if ($event instanceof TerminalEventInterface) {
                $this->printTerminalEvent($event, $output);
            }
        }

        $exitCode = $this->printResult($output);

        $this->events = [];

        return $exitCode;
    }

    public function printTerminalEvent(TerminalEventInterface $event, OutputInterface $output): void
    {
        $output->write(sprintf("%s\n\n", $this->errorColor->apply("\n\n  " . $event->getError() . "\n")));
    }

    private function printEnterEvent(EnterEvent $event, OutputInterface $output): void
    {
        $color = $this->dependencyColor;
        if (count($event->getInstantiationStack()) === 0) {
            $color = $this->rootDependencyColor;
        }

        $output->write(sprintf(str_repeat('  ', count($event->getInstantiationStack()))));
        $output->write(sprintf("└─%s\n", $color->apply($event->getDependencyName())));
    }

    private function printResult(OutputInterface $output): int
    {
        $output->write(
            sprintf(
                "\nTotal factories found: %s 🏭\n",
                $this->successfulResultColor->apply(
                    (string) $this->count(
                        [
                            InvokableEnteredEvent::class,
                            AutowireFactoryEnteredEvent::class,
                            CustomFactoryEnteredEvent::class,
                        ]
                    )
                ),
            )
        );
        $output->write(
            sprintf(
                "Custom factories skipped: %s 🛠️\n",
                $this->successfulResultColor->apply((string) $this->count([CustomFactoryEnteredEvent::class]))
            )
        );
        $output->write(
            sprintf(
                "Autowire factories analyzed: %s 🔥\n",
                $this->successfulResultColor->apply((string) $this->count([AutowireFactoryEnteredEvent::class])),
            )
        );
        $output->write(
            sprintf(
                "Invokables analyzed: %s 📦\n",
                $this->successfulResultColor->apply((string) $this->count([InvokableEnteredEvent::class])),
            )
        );
        $output->write(
            sprintf(
                "Maximum instantiation deep: %s 🏊\n",
                $this->successfulResultColor->apply((string) $this->countMaxInstantiationDeep()),
            )
        );

        $terminalEventsCount = $this->countTerminalEvents();
        if ($terminalEventsCount > 0) {
            $output->write(
                sprintf("\nTotal errors found: %s 😕\n", $this->failedResultColor->apply((string) $terminalEventsCount))
            );

            return 1;
        }

        $output->write(
            $this->successfulResultColor->apply(
                sprintf(
                    "\nAs far as I can tell, it's all good 🚀\n",
                )
            )
        );

        return 0;
    }

    /**
     * @psalm-var list<class-string> $desiredEvents
     */
    private function count(array $desiredEvents): int
    {
        $dependencies = [];
        $uniqueEvents = array_filter(
            $this->events,
            static function (EventInterface $event) use ($dependencies) {
                if (in_array($event->getDependencyName(), $dependencies, true)) {
                    return false;
                }
                $dependencies[] = $event->getDependencyName();

                return true;
            }
        );

        $desiredEvents = array_filter(
            $uniqueEvents,
            static function (EventInterface $event) use ($desiredEvents) {
                foreach ($desiredEvents as $desiredEvent) {
                    if ($event instanceof $desiredEvent) {
                        return true;
                    }
                }

                return false;
            }
        );

        return count($desiredEvents);
    }

    private function countMaxInstantiationDeep(): int
    {
        $maxInstantiationDeep = 0;
        foreach ($this->events as $event) {
            if ($event instanceof EnterEvent) {
                $deep = count($event->getInstantiationStack());
                if ($deep > $maxInstantiationDeep) {
                    $maxInstantiationDeep = $deep;
                }
            }
        }

        return $maxInstantiationDeep;
    }

    private function countTerminalEvents(): int
    {
        $count = 0;
        foreach ($this->events as $event) {
            if ($event instanceof TerminalEventInterface) {
                $count++;
            }
        }

        return $count;
    }
}
