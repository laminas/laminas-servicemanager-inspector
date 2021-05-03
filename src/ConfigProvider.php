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
use Laminas\ServiceManager\Inspector\EventCollector\ConsoleEventCollector;
use Laminas\ServiceManager\Inspector\EventCollector\EventCollectorInterface;
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
     * @psalm-return array{factories:array<string,mixed>,aliases:array<string,string>}
     */
    public function getServiceDependencies(): array
    {
        return [
            'factories' => [
                InspectCommand::class                   => ReflectionBasedAbstractFactory::class,
                ReflectionBasedDependencyScanner::class => ReflectionBasedAbstractFactory::class,
                DependencyConfig::class                 => MezzioDependencyConfigFactory::class,
                Traverser::class                        => ReflectionBasedAbstractFactory::class,
                ConsoleEventCollector::class => ReflectionBasedAbstractFactory::class,
            ],
            'aliases'   => [
                DependencyScannerInterface::class => ReflectionBasedDependencyScanner::class,
                DependencyConfigInterface::class  => DependencyConfig::class,
                TraverserInterface::class         => Traverser::class,
                EventCollectorInterface::class => ConsoleEventCollector::class,
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
