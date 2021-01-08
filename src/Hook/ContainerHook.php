<?php

declare(strict_types=1);

/**
 * @see       https://github.com/laminas/laminas-servicemanager for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\PsalmPlugin\Hook;

use Laminas\PsalmPlugin\Analyzer\ReflectionBasedFactoryAnalyzer;
use Laminas\PsalmPlugin\Traverser\Traverser;
use Laminas\PsalmPlugin\PluginConfig;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Scalar\String_;
use Psalm\Codebase;
use Psalm\CodeLocation;
use Psalm\Context;
use Psalm\Plugin\Hook\AfterMethodCallAnalysisInterface;
use Psalm\StatementsSource;
use Psalm\Type\Union;

use Throwable;

use function is_string;

final class ContainerHook implements AfterMethodCallAnalysisInterface
{
    // TODO container methods
    private const CONTAINER_CALLS = [
        'Psr\Container\ContainerInterface::get',
        'Interop\Container\ContainerInterface::get',
        'Laminas\ServiceManager\ServiceManager::get',
        'Zend\ServiceManager\ServiceManager::get',
    ];

    private static $dependencyConfig;

    private static $traverser;

    private static $dependencyDetector;

    public static function init(PluginConfig $config): void
    {
        self::$dependencyConfig = $config->getDependencyConfig();
        self::$traverser = new Traverser($config->getDependencyConfig());
        self::$dependencyDetector = new ReflectionBasedFactoryAnalyzer($config->getDependencyConfig());
    }

    public static function afterMethodCallAnalysis(
        Expr $expr,
        string $method_id,
        string $appearing_method_id,
        string $declaring_method_id,
        Context $context,
        StatementsSource $statements_source,
        Codebase $codebase,
        array &$file_replacements = [],
        Union &$return_type_candidate = null
    ): void {
        if ($declaring_method_id !== 'Psr\Container\ContainerInterface::get') {
            return;
        }

        $arg = $expr->args[0]->value;
        if ($arg instanceof String_) {
            $serviceId = $arg->value;
        } elseif ($arg instanceof ClassConstFetch) {
            $serviceId = (string) $arg->class->getAttribute('resolvedName');
            if ($arg->name != 'class') {
                $serviceId = constant(sprintf('%s::%s', $serviceId, $arg->name));
                if (!is_string($serviceId)) {
                    // @todo throw an issue
                }
            }
        } else {
            return;
        }

        try {
            (self::$traverser)($serviceId);
        } catch (Throwable $e) {
            // TODO wrap in an issue
            $codeLoction = new CodeLocation($statements_source, $expr->args[0]->value);
        }
    }
}
