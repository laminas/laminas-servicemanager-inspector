<?php

namespace Laminas\ServiceManager\Inspector\Traverser;

use Laminas\ServiceManager\Inspector\Visitor\StatsVisitorInterface;
use Throwable;

interface TraverserInterface
{
    /**
     * @psalm-var list<string> $instantiationStack
     * @param array            $instantiationStack
     * @throws Throwable
     */
    public function __invoke(Dependency $dependency, array $instantiationStack = []): void;

    public function setVisitor(StatsVisitorInterface $visitor): void;
}