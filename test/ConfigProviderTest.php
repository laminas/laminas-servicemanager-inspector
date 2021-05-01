<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\ServiceManager\Inspector;

use Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;
use Laminas\ServiceManager\Inspector\Analyzer\FactoryAnalyzerInterface;
use Laminas\ServiceManager\Inspector\Analyzer\ReflectionBasedFactoryAnalyzer;
use Laminas\ServiceManager\Inspector\Command\InspectCommand;
use Laminas\ServiceManager\Inspector\ConfigProvider;
use Laminas\ServiceManager\Inspector\DependencyConfig;
use Laminas\ServiceManager\Inspector\MezzioDependencyConfigFactory;
use Laminas\ServiceManager\Inspector\Traverser\Traverser;
use Laminas\ServiceManager\Inspector\Visitor\ConsoleStatsVisitor;
use Laminas\ServiceManager\Inspector\Visitor\StatsVisitorInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers \Laminas\ServiceManager\Inspector\ConfigProvider
 */
class ConfigProviderTest extends TestCase
{
    use ProphecyTrait;

    public function testGetConfig_Constructed_ReturnsNonEmptyArray(): void
    {
        $expectedConfig = [
            'dependencies' => [
                'factories' => [
                    InspectCommand::class                 => ReflectionBasedAbstractFactory::class,
                    ReflectionBasedFactoryAnalyzer::class => ReflectionBasedAbstractFactory::class,
                    DependencyConfig::class               => MezzioDependencyConfigFactory::class,
                    Traverser::class                      => ReflectionBasedAbstractFactory::class,
                ],
                'aliases'   => [
                    FactoryAnalyzerInterface::class => ReflectionBasedFactoryAnalyzer::class,
                    StatsVisitorInterface::class    => ConsoleStatsVisitor::class,
                ],
            ],
            'laminas-cli' => [
                'commands' => [
                    'servicemanager:inspect' => InspectCommand::class,
                ],
            ],
        ];
        $actualConfig = (new ConfigProvider())();

        self::assertSame($expectedConfig, $actualConfig);
    }
}
