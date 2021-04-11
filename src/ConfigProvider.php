<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector;

use Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;
use Laminas\ServiceManager\Inspector\Analyzer\FactoryAnalyzerInterface;
use Laminas\ServiceManager\Inspector\Analyzer\ReflectionBasedFactoryAnalyzer;
use Laminas\ServiceManager\Inspector\Visitor\ConsoleStatsVisitor;
use Laminas\ServiceManager\Inspector\Visitor\StatsVisitorInterface;
use Laminas\ServiceManager\Inspector\Command\InspectCommand;
use Laminas\ServiceManager\Inspector\Traverser\Traverser;

final class ConfigProvider
{
    /**
     * @psalm-return array{
     *     laminas-cli:array{commands: array{'servicemanager:inspect': Command\InspectCommand::class}}
     * }
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
                InspectCommand::class                 => ReflectionBasedAbstractFactory::class,
                ReflectionBasedFactoryAnalyzer::class => ReflectionBasedAbstractFactory::class,
                DependencyConfig::class               => MezzioDependencyConfigFactory::class,
                Traverser::class                      => ReflectionBasedAbstractFactory::class,
            ],
            'aliases'   => [
                FactoryAnalyzerInterface::class => ReflectionBasedFactoryAnalyzer::class,
                StatsVisitorInterface::class => ConsoleStatsVisitor::class,
            ],
        ];
    }

    /**
     * @psalm-return array{commands: array{'migration:phpstorm-extended-meta': Command\InspectCommand::class}}
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
