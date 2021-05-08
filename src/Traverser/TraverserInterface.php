<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\Traverser;

use Laminas\ServiceManager\Inspector\Dependency\Dependency;
use Throwable;

interface TraverserInterface
{
    /**
     * @psalm-var list<string> $instantiationStack
     * @throws Throwable
     */
    public function __invoke(Dependency $dependency, array $instantiationStack = []): void;
}
