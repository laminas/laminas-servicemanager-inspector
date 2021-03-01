<?php

declare(strict_types=1);

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ServiceManager\Inspector\Hook;

use Laminas\ServiceManager\Inspector\Analyzer\ReflectionBasedFactoryAnalyzer;
use Laminas\ServiceManager\Inspector\DependencyConfig;
use Laminas\ServiceManager\Inspector\Exception\IssuableInterface;
use Laminas\ServiceManager\Inspector\Issue\InvalidConfigIssue;
use Laminas\ServiceManager\Inspector\PluginConfig;
use Laminas\ServiceManager\Inspector\Traverser\Dependency;
use Laminas\ServiceManager\Inspector\Traverser\Traverser;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Scalar\String_;
use Psalm\Codebase;
use Psalm\CodeLocation;
use Psalm\Context;
use Psalm\Issue\PluginIssue;
use Psalm\IssueBuffer;
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

    /**
     * @var PluginConfig
     */
    private static $pluginConfig;

    private static $dependencyConfig;

    private static $traverser;

    public static function init(PluginConfig $pluginConfig): void
    {
        self::$pluginConfig = $pluginConfig;
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
        if (! in_array($declaring_method_id, self::CONTAINER_CALLS, true)) {
            return;
        }

        $arg = $expr->args[0]->value;
        if ($arg instanceof String_) {
            $serviceId = $arg->value;
        } elseif ($arg instanceof ClassConstFetch) {
            $serviceId = (string)$arg->class->getAttribute('resolvedName');
            if ($arg->name != 'class') {
                $serviceId = constant(sprintf('%s::%s', $serviceId, $arg->name));
                if (! is_string($serviceId)) {
                    // TODO throw an issue
                }
            }
        } else {
            return;
        }

        try {
            (self::getTraverser())(new Dependency($serviceId));
        } catch (Throwable $e) {
            // TODO wrap in an issue
            $codeLocation = new CodeLocation($statements_source, $expr->args[0]->value);
            IssueBuffer::accepts(self::buildIssue($e, $codeLocation));
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

    private static function buildIssue(Throwable $e, CodeLocation $codeLocation): PluginIssue
    {
        if ($e instanceof IssuableInterface) {
            return $e->toIssue($codeLocation);
        }

        return new InvalidConfigIssue($e->getMessage(), $codeLocation);
    }
}
