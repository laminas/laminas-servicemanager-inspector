<?php

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\Command;

use Laminas\ServiceManager\Inspector\Dependency\Dependency;
use Laminas\ServiceManager\Inspector\DependencyConfig\DependencyConfigInterface;
use Laminas\ServiceManager\Inspector\EventCollector\EventCollectorInterface;
use Laminas\ServiceManager\Inspector\EventReporter\EventReporterInterface;
use Laminas\ServiceManager\Inspector\Scanner\DependencyScannerInterface;
use Laminas\ServiceManager\Inspector\Traverser\TraverserInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

use function array_keys;

final class InspectCommand extends Command
{
    /** @var string|null $defaultName */
    public static $defaultName = 'servicemanager:inspect';

    private DependencyConfigInterface $config;

    private DependencyScannerInterface $dependencyScanner;

    private TraverserInterface $traverser;

    private EventCollectorInterface $eventCollector;

    private EventReporterInterface $eventReporter;

    public function __construct(
        DependencyConfigInterface $config,
        DependencyScannerInterface $dependencyScanner,
        TraverserInterface $traverser,
        EventCollectorInterface $eventCollector,
        EventReporterInterface $eventReporter
    ) {
        $this->config            = $config;
        $this->dependencyScanner = $dependencyScanner;
        $this->traverser         = $traverser;
        $this->eventCollector    = $eventCollector;
        $this->eventReporter     = $eventReporter;

        parent::__construct(self::$defaultName);
    }

    protected function configure(): void
    {
        // FIXME refine
        $this->setDescription('Inspects for autowire-related ServiceManager configuration defects');
        $this->setHelp('Prints a detailed autowire-related report. Returns a non-zero code upon any defect.');
    }

    /**
     * @throws Throwable
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->scan();

        $exitCode = $this->eventCollector->hasTerminalEvent() ? 1 : 0;

        ($this->eventReporter)($this->eventCollector->release(), $output);

        return $exitCode;
    }

    /**
     * @throws Throwable
     */
    private function scan(): void
    {
        foreach (array_keys($this->config->getFactories()) as $serviceName) {
            if ($this->dependencyScanner->canScan($serviceName)) {
                ($this->traverser)(new Dependency($serviceName));
            }
        }
    }
}
