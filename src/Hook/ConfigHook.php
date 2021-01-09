<?php

declare(strict_types=1);

/**
 * @see       https://github.com/laminas/laminas-servicemanager for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\PsalmPlugin\Hook;

use Laminas\PsalmPlugin\Analyzer\FactoryAnalyzerInterface;
use Laminas\PsalmPlugin\Analyzer\ReflectionBasedFactoryAnalyzer;
use Laminas\PsalmPlugin\DependencyConfig;
use Laminas\PsalmPlugin\Traverser\Dependency;
use Laminas\PsalmPlugin\Traverser\Traverser;
use Laminas\PsalmPlugin\PluginConfig;
use Psalm\Codebase;
use Psalm\Plugin\Hook\AfterAnalysisInterface;
use Psalm\SourceControl\SourceControlInfo;

final class ConfigHook implements AfterAnalysisInterface
{
    private static $pluginConfig;

    private static $dependencyConfig;

    private static $traverser;

    private static $factoryAnalyzer;

    public static function init(PluginConfig $pluginConfig): void
    {
        self::$pluginConfig = $pluginConfig;
    }

    public static function afterAnalysis(
        Codebase $codebase,
        array $issues,
        array $build_info,
        ?SourceControlInfo $source_control_info = null
    ): void {
        foreach (self::getDependencyConfig()->getFactories() as $serviceName => $_) {
            if (self::getFactoryAnalyzer()->canDetect($serviceName)) {
                (self::getTraverser())((new Dependency($serviceName)));
            }
        }
    }

    private static function getDependencyConfig(): DependencyConfig
    {
        if (self::$dependencyConfig === null) {
            self::$dependencyConfig = self::$pluginConfig->getDependencyConfig();
        }

        return self::$dependencyConfig;
    }

    private static function getTraverser(): Traverser
    {
        if (self::$traverser === null) {
            self::$traverser = new Traverser(
                self::getDependencyConfig(),
                new ReflectionBasedFactoryAnalyzer(self::getDependencyConfig())
            );
        }

        return self::$traverser;
    }

    private static function getFactoryAnalyzer(): FactoryAnalyzerInterface
    {
        if (self::$factoryAnalyzer === null) {
            self::$factoryAnalyzer = new ReflectionBasedFactoryAnalyzer(self::getDependencyConfig());
        }

        return self::$traverser;
    }
}
