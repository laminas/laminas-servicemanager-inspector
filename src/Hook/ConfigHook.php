<?php

declare(strict_types=1);

/**
 * @see       https://github.com/laminas/laminas-servicemanager for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\PsalmPlugin\Hook;

use Laminas\PsalmPlugin\DependencyDetector\ReflectionBasedDependencyDetector;
use Laminas\PsalmPlugin\Traverser\Dependency;
use Laminas\PsalmPlugin\Traverser\Traverser;
use Laminas\PsalmPlugin\PluginConfig;
use Psalm\Codebase;
use Psalm\Plugin\Hook\AfterAnalysisInterface;
use Psalm\SourceControl\SourceControlInfo;

final class ConfigHook implements AfterAnalysisInterface
{
    private static $dependencyConfig;

    private static $traverser;

    private static $dependencyDetector;

    public static function init(PluginConfig $config): void
    {
        self::$dependencyConfig = $config->getDependencyConfig();
        self::$traverser = new Traverser($config->getDependencyConfig());
        self::$dependencyDetector = new ReflectionBasedDependencyDetector($config->getDependencyConfig());
    }

    public static function afterAnalysis(
        Codebase $codebase,
        array $issues,
        array $build_info,
        ?SourceControlInfo $source_control_info = null
    ): void {
        foreach (self::$dependencyConfig->getFactories() as $serviceName => $_) {
            if (self::$dependencyDetector->canDetect($serviceName)) {
                (self::$traverser)((new Dependency($serviceName)));
            }
        }
    }
}
