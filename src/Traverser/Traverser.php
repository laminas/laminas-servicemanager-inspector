<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\Traverser;

use Laminas\ServiceManager\Inspector\Analyzer\FactoryAnalyzerInterface;
use Laminas\ServiceManager\Inspector\DependencyConfig;
use Laminas\ServiceManager\Inspector\Exception\CircularDependencyException;
use Laminas\ServiceManager\Inspector\Exception\MissingFactoryException;
use Laminas\ServiceManager\Inspector\Visitor\NullStatsVisitor;
use Laminas\ServiceManager\Inspector\Visitor\StatsVisitorInterface;
use Throwable;

use function in_array;

final class Traverser
{
    /** @var DependencyConfig */
    private $config;

    /** @var FactoryAnalyzerInterface */
    private $factoryAnalyzer;

    /** @var StatsVisitorInterface */
    private $visitor;

    public function __construct(
        DependencyConfig $config,
        FactoryAnalyzerInterface $factoryAnalyzer
    ) {
        $this->config          = $config;
        $this->factoryAnalyzer = $factoryAnalyzer;
        $this->visitor = new NullStatsVisitor();
    }

    /**
     * @psalm-var list<string> $instantiationStack
     * @param array $instantiationStack
     * @throws Throwable
     */
    public function __invoke(Dependency $dependency, array $instantiationStack = []): void
    {
        $this->assertHasFactory($dependency, $instantiationStack);
        $this->assertNotCircularDependency($dependency, $instantiationStack);

        $instantiationStack[] = $dependency->getName();

        $dependencies = $this->factoryAnalyzer->detect($dependency->getName());
        foreach ($dependencies as $childDependency) {
            $this($childDependency, $instantiationStack);
        }
    }

    public function setVisitor(StatsVisitorInterface $visitor): void
    {
        $this->visitor = $visitor;
    }

    private function assertHasFactory(Dependency $dependency, array $instantiationStack): void
    {
        $isInvokable = $this->config->isInvokable($dependency->getName());
        if ($isInvokable) {
            $this->visitor->enterInvokable($dependency->getName(), $instantiationStack);
        }

        $hasAutowireFactory = $this->config->hasAutowireFactory($dependency->getName());
        if ($hasAutowireFactory) {
            $this->visitor->enterAutowireFactory($dependency->getName(), $instantiationStack);
        }

        $hasFactory  = $this->config->hasFactory($dependency->getName());
        if ($hasFactory && !$isInvokable) {
            $this->visitor->enterCustomFactory($dependency->getName(), $instantiationStack);
        }

        $isOptional  = $dependency->isOptional();
        if ($isInvokable || $hasAutowireFactory || $hasFactory || $isOptional) {
            return;
        }

        $this->visitor->enterError($dependency->getName(), $instantiationStack);

        throw new MissingFactoryException($dependency->getName());
    }

    /**
     * @psalm-var list<string> $instantiationStack
     * @param array $instantiationStack
     */
    private function assertNotCircularDependency(Dependency $dependency, array $instantiationStack): void
    {
        if (in_array($dependency->getName(), $instantiationStack, true)) {
            $this->visitor->enterError($dependency->getName(), $instantiationStack);

            throw new CircularDependencyException($dependency->getName(), $instantiationStack);
        }
    }
}
