<?php

declare(strict_types=1);

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ServiceManager\Inspector\Traverser;

use Laminas\ServiceManager\Inspector\Analyzer\FactoryAnalyzerInterface;
use Laminas\ServiceManager\Inspector\DependencyConfig;
use Laminas\ServiceManager\Inspector\Exception\CircularDependencyException;
use Laminas\ServiceManager\Inspector\Exception\MissingFactoryException;
use Throwable;

use function in_array;

final class Traverser
{
    /**
     * @var DependencyConfig
     */
    private $config;

    /**
     * @var FactoryAnalyzerInterface
     */
    private $factoryAnalyzer;

    /**
     * @param DependencyConfig $config
     * @param FactoryAnalyzerInterface $factoryAnalyzer
     */
    public function __construct(
        DependencyConfig $config,
        FactoryAnalyzerInterface $factoryAnalyzer
    ) {
        $this->config = $config;
        $this->factoryAnalyzer = $factoryAnalyzer;
    }

    /**
     * @psalm-var list<string> $instantiationStack
     *
     * @param Dependency $dependency
     * @param array $instantiationStack
     * @throws Throwable
     */
    public function __invoke(Dependency $dependency, array $instantiationStack = []): void
    {
        $this->assertHasFactory($dependency);
        $this->assertNotCircularDependency($dependency, $instantiationStack);

        $instantiationStack[] = $dependency->getName();

        $dependencies = $this->factoryAnalyzer->detect($dependency->getName());
        foreach ($dependencies as $childDependency) {
            ($this)($childDependency, $instantiationStack);
        }
    }

    /**
     * @param Dependency $dependency
     */
    private function assertHasFactory(Dependency $dependency): void
    {
        $isInvokable = $this->config->isInvokable($dependency->getName());
        $hasFactory = $this->config->hasFactory($dependency->getName());
        $isOptional = $dependency->isOptional();
        if ($isInvokable || $hasFactory || $isOptional) {
            return;
        }

        throw new MissingFactoryException($dependency->getName());
    }

    /**
     * @psalm-var list<string> $instantiationStack
     *
     * @param Dependency $dependency
     * @param array $instantiationStack
     */
    private function assertNotCircularDependency(Dependency $dependency, array $instantiationStack): void
    {
        if (in_array($dependency->getName(), $instantiationStack, true)) {
            throw new CircularDependencyException($dependency->getName(), $instantiationStack);
        }
    }
}
