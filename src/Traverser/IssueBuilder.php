<?php

declare(strict_types=1);

/**
 * @see       https://github.com/laminas/laminas-servicemanager for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\PsalmPlugin\Traverser;

use Psalm\Codebase;
use Psalm\CodeLocation;
use Psalm\Issue\PluginIssue;
use Throwable;

final class IssueBuilder
{
    public static function fromContainerException(Throwable $e, CodeLocation $location): PluginIssue
    {

    }

    public static function fromConfigException(Throwable $e, Codebase $codebase): PluginIssue
    {

    }
}