<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\Exception;

use Psalm\CodeLocation;
use Psalm\Issue\PluginIssue;

interface IssuableInterface
{
    public function toIssue(CodeLocation $codeLocation): PluginIssue;
}
