<?php

declare(strict_types=1);

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ServiceManager\Inspector\Issue;

use Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;
use LogicException;
use Psalm\CodeLocation;
use Psalm\Issue\PluginIssue;
use Throwable;

use function sprintf;

final class UnexpectedScalarTypeIssue extends PluginIssue
{
    public function __construct(string $serviceName, string $paramName, CodeLocation $codeLocation)
    {
        parent::__construct(
            sprintf(
                "ReflectionBasedAbstractFactory cannot resolve scalar '%s' for '%s' service.",
                $paramName,
                $serviceName
            ),
            $codeLocation
        );
    }
}
