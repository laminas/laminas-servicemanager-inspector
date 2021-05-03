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
use Laminas\ServiceManager\Inspector\Event\AutowireFactoryEnteredEventInterface;
use Laminas\ServiceManager\Inspector\Event\CircularDependencyDetectedEvent;
use Laminas\ServiceManager\Inspector\Event\CustomFactoryEnteredEventInterface;
use Laminas\ServiceManager\Inspector\Event\InvokableEnteredEventInterface;
use Laminas\ServiceManager\Inspector\Event\MissingFactoryDetectedEvent;
use Laminas\ServiceManager\Inspector\EventCollector\EventCollectorInterface;
use Laminas\ServiceManager\Inspector\Scanner\DependencyScannerInterface;
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
        $this->eventCollector    = $eventCollector;
    }

    /**
     * @psalm-var list<string> $instantiationStack
     * @param array            $instantiationStack
     * @throws Throwable
     */
    public function __invoke(Dependency $dependency, array $instantiationStack = []): void
    {
        if (! $this->hasFactory($dependency, $instantiationStack)) {
            return;
        }

        if ($this->hasCircularDependency($dependency, $instantiationStack)) {
            return;
        }

        $instantiationStack[] = $dependency->getName();

        $dependencies = $this->dependencyScanner->scan($dependency->getName());
        foreach ($dependencies as $childDependency) {
            $this($childDependency, $instantiationStack);
        }
    }

    private function hasFactory(Dependency $dependency, array $instantiationStack): bool
    {
        $isInvokable = $this->config->isInvokable($dependency->getName());
        if ($isInvokable) {
            $this->eventCollector->collect(
                new InvokableEnteredEventInterface($dependency->getName(), $instantiationStack)
            );
        }

        $hasAutowireFactory = $this->config->hasAutowireFactory($dependency->getName());
        if ($hasAutowireFactory) {
            $this->eventCollector->collect(
                new AutowireFactoryEnteredEventInterface($dependency->getName(), $instantiationStack)
            );
        }

        $hasFactory = $this->config->hasFactory($dependency->getName());
        if ($hasFactory && ! $isInvokable) {
            $this->eventCollector->collect(
                new CustomFactoryEnteredEventInterface($dependency->getName(), $instantiationStack)
            );
        }

        $isOptional = $dependency->isOptional();
        if ($isInvokable || $hasAutowireFactory || $hasFactory || $isOptional) {
            return true;
        }

        $event = new MissingFactoryDetectedEvent($dependency->getName(), $instantiationStack);
        $this->eventCollector->collect($event);

        return false;
    }

    /**
     * @psalm-var list<string> $instantiationStack
     * @param array            $instantiationStack
     */
    private function hasCircularDependency(Dependency $dependency, array $instantiationStack): bool
    {
        if (in_array($dependency->getName(), $instantiationStack, true)) {
            $event = new CircularDependencyDetectedEvent($dependency->getName(), $instantiationStack);
            $this->eventCollector->collect($event);

            return true;
        }

        return false;
    }
}
