<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector;

use Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;
use Laminas\ServiceManager\Inspector\Command\InspectCommand;
use Laminas\ServiceManager\Inspector\DependencyConfig\DependencyConfig;
use Laminas\ServiceManager\Inspector\DependencyConfig\DependencyConfigInterface;
use Laminas\ServiceManager\Inspector\DependencyConfig\MezzioDependencyConfigFactory;
use Laminas\ServiceManager\Inspector\EventCollector\EventCollector;
use Laminas\ServiceManager\Inspector\EventCollector\EventCollectorInterface;
use Laminas\ServiceManager\Inspector\EventCollector\NullEventCollector;
use Laminas\ServiceManager\Inspector\EventReporter\ConsoleColor\ConsoleColor;
use Laminas\ServiceManager\Inspector\EventReporter\ConsoleColor\ConsoleColorInterface;
use Laminas\ServiceManager\Inspector\EventReporter\ConsoleColor\NullConsoleColor;
use Laminas\ServiceManager\Inspector\EventReporter\ConsoleDetailedEventReporter;
use Laminas\ServiceManager\Inspector\EventReporter\ConsoleSummaryEventReporter;
use Laminas\ServiceManager\Inspector\EventReporter\EventReporterInterface;
use Laminas\ServiceManager\Inspector\EventReporter\NullEventReporter;
use Laminas\ServiceManager\Inspector\Scanner\DependencyScannerInterface;
use Laminas\ServiceManager\Inspector\Scanner\ReflectionBasedDependencyScanner;
use Laminas\ServiceManager\Inspector\Traverser\Traverser;
use Laminas\ServiceManager\Inspector\Traverser\TraverserInterface;

final class ConfigProvider
{
    /**
     * @psalm-return array<string, mixed>
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getServiceDependencies(),
            'laminas-cli'  => $this->laminasCliConfiguration(),
        ];
    }

    /**
     * @psalm-return array{factories:array<string,class-string>,aliases:array<string,string>}
     */
    public function getServiceDependencies(): array
    {
        return [
            'factories' => [
                InspectCommand::class                   => ReflectionBasedAbstractFactory::class,
                ReflectionBasedDependencyScanner::class => ReflectionBasedAbstractFactory::class,
                // TODO laminas config
                DependencyConfig::class             => MezzioDependencyConfigFactory::class,
                Traverser::class                    => ReflectionBasedAbstractFactory::class,
                EventCollector::class               => ReflectionBasedAbstractFactory::class,
                NullEventCollector::class           => ReflectionBasedAbstractFactory::class,
                ConsoleDetailedEventReporter::class => ReflectionBasedAbstractFactory::class,
                ConsoleSummaryEventReporter::class  => ReflectionBasedAbstractFactory::class,
                NullEventReporter::class            => ReflectionBasedAbstractFactory::class,
                ConsoleColor::class                 => ReflectionBasedAbstractFactory::class,
                NullConsoleColor::class             => ReflectionBasedAbstractFactory::class,
            ],
            'aliases'   => [
                DependencyScannerInterface::class => ReflectionBasedDependencyScanner::class,
                DependencyConfigInterface::class  => DependencyConfig::class,
                TraverserInterface::class         => Traverser::class,
                EventCollectorInterface::class    => EventCollector::class,
                EventReporterInterface::class     => ConsoleDetailedEventReporter::class,
                ConsoleColorInterface::class      => ConsoleColor::class,
            ],
        ];
    }

    /**
     * @psalm-return array<string, mixed>
     */
    private function laminasCliConfiguration(): array
    {
        return [
            'commands' => [
                'servicemanager:inspect' => InspectCommand::class,
            ],
        ];
    }
}
