<?php

declare(strict_types=1);

/**
 * @see       https://github.com/laminas/laminas-servicemanager for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\PsalmPlugin;

use Laminas\PsalmPlugin\Collector\StatsCollectorInterface;
use Laminas\PsalmPlugin\DependencyDetector\DependencyDetectorInterface;
use Laminas\PsalmPlugin\Exception\CircularDependencyIssue;
use Laminas\PsalmPlugin\Exception\MissingFactoryIssue;
use Throwable;

use function in_array;

final class Inspector
{
    /**
     * @var DependencyConfig
     */
    private DependencyConfig $config;

    /**
     * @var DependencyDetectorInterface
     */
    private DependencyDetectorInterface $dependenciesDetector;


    /**
     * @param DependencyConfig $config
     * @param DependencyDetectorInterface $dependenciesDetector
     */
    public function __construct(
        DependencyConfig $config,
        DependencyDetectorInterface $dependenciesDetector,
    ) {
        $this->config = $config;
        $this->dependenciesDetector = $dependenciesDetector;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(): void
    {
        foreach ($this->config->getFactories() as $serviceName => $_) {
            if ($this->dependenciesDetector->canDetect($serviceName)) {
                $this->walk(new Dependency($serviceName));
            }
        }
    }

    /**
     * @psalm-var list<string> $instantiationStack
     *
     * @param Dependency $dependency
     * @param array $instantiationStack
     * @throws Throwable
     */
    public function walk(Dependency $dependency, array $instantiationStack = []): void
    {
        $this->assertNotCircularDependency($dependency, $instantiationStack);

        $instantiationStack[] = $dependency->getName();


        $dependencies = $this->dependenciesDetector->detect($dependency->getName());
        foreach ($dependencies as $childDependency) {
            if (! $this->config->hasFactory($childDependency->getName()) && ! $childDependency->isOptional()) {
                throw new MissingFactoryIssue($childDependency->getName());
            }

            $this->walk($childDependency, $instantiationStack);
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
            throw new CircularDependencyIssue($dependency->getName(), $instantiationStack);
        }
    }
}
