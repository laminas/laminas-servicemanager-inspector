<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\Hook;

use Laminas\ServiceManager\Inspector\Analyzer\FactoryAnalyzerInterface;
use Laminas\ServiceManager\Inspector\Analyzer\ReflectionBasedFactoryAnalyzer;
use Laminas\ServiceManager\Inspector\DependencyConfig;
use Laminas\ServiceManager\Inspector\Exception\IssuableInterface;
use Laminas\ServiceManager\Inspector\Issue\InvalidConfigIssue;
use Laminas\ServiceManager\Inspector\PluginConfig;
use Laminas\ServiceManager\Inspector\Traverser\Dependency;
use Laminas\ServiceManager\Inspector\Traverser\Traverser;
use Psalm\Codebase;
use Psalm\CodeLocation;
use Psalm\Context;
use Psalm\Issue\PluginIssue;
use Psalm\IssueBuffer;
use Psalm\Plugin\Hook\AfterFileAnalysisInterface;
use Psalm\StatementsSource;
use Psalm\Storage\FileStorage;
use Throwable;

final class ConfigHook implements AfterFileAnalysisInterface
{
    /** @var PluginConfig */
    private static $pluginConfig;

    /** @var null|DependencyConfig */
    private static $dependencyConfig;

    /** @var null|Traverser */
    private static $traverser;

    /** @var null|FactoryAnalyzerInterface */
    private static $factoryAnalyzer;

    public static function init(PluginConfig $pluginConfig): void
    {
        self::$pluginConfig = $pluginConfig;
    }

    public static function afterAnalyzeFile(
        StatementsSource $statementsSource,
        Context $fileContext,
        FileStorage $fileStorage,
        Codebase $codebase
    ): void {
        if ($fileStorage->file_path !== self::$pluginConfig->getDependencyConfigPath()) {
            return;
        }

        // phpcs:ignore WebimpressCodingStandard.NamingConventions.ValidVariableName.NotCamelCaps
        foreach (self::getDependencyConfig()->getFactories() as $serviceName => $_) {
            if (self::getFactoryAnalyzer()->canDetect($serviceName)) {
                try {
                    (self::getTraverser())(new Dependency($serviceName));
                } catch (Throwable $e) {
                    IssueBuffer::add(self::buildIssue($e));
                }
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

        return self::$factoryAnalyzer;
    }

    private static function buildIssue(Throwable $e): PluginIssue
    {
        $codeLocation = self::buildConfigCodeLocation();

        if ($e instanceof IssuableInterface) {
            return $e->toIssue($codeLocation);
        }

        return new InvalidConfigIssue($e->getMessage(), $codeLocation);
    }

    private static function buildConfigCodeLocation(): CodeLocation
    {
        $path = self::$pluginConfig->getDependencyConfigPath();

        return new CodeLocation\Raw('', $path, 'config/web/config.php', 0, 0);
    }
}
