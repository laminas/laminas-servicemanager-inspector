<?php

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\Traverser;

use Laminas\ServiceManager\Inspector\Dependency\Dependency;
use Throwable;

interface TraverserInterface
{
    /**
     * @psalm-param list<string> $instantiationStack
     * @param string[] $instantiationStack
     * @throws Throwable
     */
    public function __invoke(Dependency $dependency, array $instantiationStack = []): void;
}
