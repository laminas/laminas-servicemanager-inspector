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
use Laminas\PsalmPlugin\Exception\IssuableInterface;
use Laminas\PsalmPlugin\Issue\InvalidConfigIssue;
use Laminas\PsalmPlugin\Traverser\Dependency;
use Laminas\PsalmPlugin\Traverser\Traverser;
use Laminas\PsalmPlugin\PluginConfig;
use Psalm\Codebase;
use Psalm\CodeLocation;
use Psalm\Context;
use Psalm\Internal\Scanner\FileScanner;
use Psalm\Issue\PluginIssue;
use Psalm\IssueBuffer;
use Psalm\Plugin\Hook\AfterFileAnalysisInterface;
use Psalm\SourceControl\SourceControlInfo;
use Psalm\StatementsSource;
use Psalm\Storage\FileStorage;
use Throwable;

final class ConfigHook implements AfterFileAnalysisInterface
{
    /**
     * @var PluginConfig
     */
    private static $pluginConfig;

    private static $dependencyConfig;

    private static $traverser;

    private static $factoryAnalyzer;

    public static function init(PluginConfig $pluginConfig): void
    {
        self::$pluginConfig = $pluginConfig;
    }

    public static function afterAnalyzeFile(
        StatementsSource $statements_source,
        Context $file_context,
        FileStorage $file_storage,
        Codebase $codebase
    ): void {
        if ($file_storage->file_path !== self::$pluginConfig->getDependencyConfigPath()) {
            return;
        }

        foreach (self::getDependencyConfig()->getFactories() as $serviceName => $_) {
            if (self::getFactoryAnalyzer()->canDetect($serviceName)) {
                try {
                    (self::getTraverser())((new Dependency($serviceName)));
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

        return  new CodeLocation\Raw('', $path, 'config/web/config.php', 0, 0);
    }
}
