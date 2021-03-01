<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\Issue;

use Psalm\CodeLocation;
use Psalm\Issue\PluginIssue;
use Throwable;

use function sprintf;

final class MissingFactoryIssue extends PluginIssue
{
    /**
     * @param Throwable|null $previous
     */
    public function __construct(string $name, CodeLocation $codeLocation)
    {
        parent::__construct(sprintf("No factory is provided for '%s' service.", $name), $codeLocation);
    }
}
