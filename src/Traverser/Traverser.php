<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\Traverser;

use Laminas\ServiceManager\Inspector\DependencyConfig;
use Laminas\ServiceManager\Inspector\DependencyConfigInterface;
use Laminas\ServiceManager\Inspector\EventCollector\EventCollectorInterface;
use Laminas\ServiceManager\Inspector\Exception\CircularDependencyException;
use Laminas\ServiceManager\Inspector\Exception\MissingFactoryException;
use Laminas\ServiceManager\Inspector\Scanner\DependencyScannerInterface;
use Laminas\ServiceManager\Inspector\EventCollector\NullListener;
use Laminas\ServiceManager\Inspector\EventCollector\ListenerInterface;
use Throwable;

use function in_array;

final class Traverser implements TraverserInterface
{
    /** @var DependencyConfig */
    private $config;

    /** @var DependencyScannerInterface */
    private $dependencyScanner;

    /** @var EventCollectorInterface */
    private $eventCollector;

    public function __construct(
        DependencyConfigInterface $config,
        DependencyScannerInterface $dependencyScanner,
        EventCollectorInterface $eventCollector
    ) {
        $this->config            = $config;
        $this->dependencyScanner = $dependencyScanner;
        $this->eventCollector = $eventCollector;
    }

    /**
     * @psalm-var list<string> $instantiationStack
     * @param array            $instantiationStack
     * @throws Throwable
     */
    public function __invoke(Dependency $dependency, array $instantiationStack = []): void
    {
        $this->assertHasFactory($dependency, $instantiationStack);
        $this->assertNotCircularDependency($dependency, $instantiationStack);

        $instantiationStack[] = $dependency->getName();

        $dependencies = $this->dependencyScanner->scan($dependency->getName());
        foreach ($dependencies as $childDependency) {
            $this($childDependency, $instantiationStack);
        }
    }

    public function setVisitor(ListenerInterface $visitor): void
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

        $hasFactory = $this->config->hasFactory($dependency->getName());
        if ($hasFactory && ! $isInvokable) {
            $this->visitor->enterCustomFactory($dependency->getName(), $instantiationStack);
        }

        $isOptional = $dependency->isOptional();
        if ($isInvokable || $hasAutowireFactory || $hasFactory || $isOptional) {
            return;
        }

        $this->visitor->enterError($dependency->getName(), $instantiationStack);

        throw new MissingFactoryException($dependency->getName());
    }

    /**
     * @psalm-var list<string> $instantiationStack
     * @param array            $instantiationStack
     */
    private function assertNotCircularDependency(Dependency $dependency, array $instantiationStack): void
    {
        if (in_array($dependency->getName(), $instantiationStack, true)) {
            $this->visitor->enterError($dependency->getName(), $instantiationStack);

            throw new CircularDependencyException($dependency->getName(), $instantiationStack);
        }
    }
}
