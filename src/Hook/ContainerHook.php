<?php

declare(strict_types=1);

/**
 * @see       https://github.com/laminas/laminas-servicemanager for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\PsalmPlugin\Hook;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Scalar\String_;
use Psalm\Codebase;
use Psalm\Context;
use Psalm\Plugin\Hook\AfterMethodCallAnalysisInterface;
use Psalm\StatementsSource;
use Psalm\Type\Union;

final class ContainerHook implements AfterMethodCallAnalysisInterface
{
    private const CONTAINER_CALLS = [
        'Psr\Container\ContainerInterface::get',
        'Interop\Container\ContainerInterface',
        'Laminas\ServiceManager\ServiceManager::get',
        'Zend\ServiceManager\ServiceManager::get',
    ];

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
            }
        } else {
            return;
        }

        $this->inspector->walk(new Dependency($serviceId));
    }
}
