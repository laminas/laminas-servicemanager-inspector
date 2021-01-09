<?php

declare(strict_types=1);

/**
 * @see       https://github.com/laminas/laminas-servicemanager for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\PsalmPlugin\Traverser;

use Laminas\PsalmPlugin\Analyzer\FactoryAnalyzerInterface;
use Laminas\PsalmPlugin\DependencyConfig;
use Laminas\PsalmPlugin\Exception\CircularDependencyException;
use Laminas\PsalmPlugin\Exception\MissingFactoryException;
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
        if (! $this->config->hasFactory($dependency->getName()) && ! $dependency->isOptional()) {
            throw new MissingFactoryException($dependency->getName());
        }

        $this->assertNotCircularDependency($dependency, $instantiationStack);

        $instantiationStack[] = $dependency->getName();

        $dependencies = $this->factoryAnalyzer->detect($dependency->getName());
        foreach ($dependencies as $childDependency) {
            ($this)($childDependency, $instantiationStack);
        }
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
