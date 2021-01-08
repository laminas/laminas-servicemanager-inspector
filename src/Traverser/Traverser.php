<?php

declare(strict_types=1);

/**
 * @see       https://github.com/laminas/laminas-servicemanager for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\PsalmPlugin\Traverser;

use Throwable;

use function in_array;

final class Traverser
{
    /**
     * @var DependencyConfig
     */
    private $config;

    /**
     * @param DependencyConfig $config
     */
    public function __construct(
        DependencyConfig $config
    ) {
        $this->config = $config;
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
        $this->assertNotCircularDependency($dependency, $instantiationStack);

        $instantiationStack[] = $dependency->getName();

        $dependencies = $this->dependenciesDetector->detect($dependency->getName());
        foreach ($dependencies as $childDependency) {
            if (! $this->config->hasFactory($childDependency->getName()) && ! $childDependency->isOptional()) {
                throw new MissingFactoryIssue($childDependency->getName());
            }

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
            throw new CircularDependencyIssue($dependency->getName(), $instantiationStack);
        }
    }
}
