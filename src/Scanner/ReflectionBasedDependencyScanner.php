<?php

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\Scanner;

use Laminas\ServiceManager\Inspector\Dependency\Dependency;
use Laminas\ServiceManager\Inspector\DependencyConfig\DependencyConfigInterface;
use Laminas\ServiceManager\Inspector\Event\UnresolvableParameterDetectedEvent;
use Laminas\ServiceManager\Inspector\EventCollector\EventCollectorInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionParameter;

use function class_exists;
use function in_array;
use function interface_exists;
use function is_string;

final class ReflectionBasedDependencyScanner implements DependencyScannerInterface
{
    private const SUPPORTED_FACTORIES = [
        // phpcs:ignore
        'Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory',
        // phpcs:ignore
        'Zend\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory',
    ];

    private DependencyConfigInterface $config;

    private EventCollectorInterface $eventCollector;

    public function __construct(DependencyConfigInterface $config, EventCollectorInterface $eventCollector)
    {
        $this->config         = $config;
        $this->eventCollector = $eventCollector;
    }

    /**
     * @psalm-return list<Dependency>
     * @return Dependency[]
     * @throws ReflectionException
     */
    public function scan(string $serviceName): array
    {
        if (! $this->canScan($serviceName)) {
            return [];
        }

        // TODO throw an event on interface

        $realServiceName = $this->config->getRealName($serviceName);
        // TODO check if invokable has zero params
        if ($this->config->isInvokable($realServiceName)) {
            return [];
        }

        return $this->getConstructorParameters($serviceName);
    }

    public function canScan(string $serviceName): bool
    {
        $class = $this->config->getFactory($serviceName);

        return in_array($class, self::SUPPORTED_FACTORIES, true);
    }

    /**
     * @psalm-return list<Dependency>
     * @return Dependency[]
     * @throws ReflectionException
     */
    private function getConstructorParameters(string $serviceName): array
    {
        /** @psalm-var class-string $serviceName */
        $reflectionClass = new ReflectionClass($serviceName);
        $constructor     = $reflectionClass->getConstructor();
        if ($constructor === null) {
            return [];
        }

        $unsatisfiedDependencies = [];
        foreach ($constructor->getParameters() as $parameter) {
            $type       = $parameter->getType();
            $type       = $type instanceof ReflectionNamedType ? $type->getName() : null;
            $isNotClass = is_string($type) && ! class_exists($type) && ! interface_exists($type);
            if ($type === null || $isNotClass) {
                if (! $parameter->isDefaultValueAvailable()) {
                    $event = new UnresolvableParameterDetectedEvent($serviceName, $parameter->getName());
                    ($this->eventCollector)($event);
                }

                continue;
            }

            $class = $type;
            /** @psalm-var string $className */
            $realDependencyName = $this->config->getRealName($class);

            $unsatisfiedDependencies[] = new Dependency($realDependencyName, $this->isOptional($parameter));
        }

        return $unsatisfiedDependencies;
    }

    private function isOptional(ReflectionParameter $parameter): bool
    {
        return $parameter->isOptional() || ($parameter->hasType() && $parameter->getType()->allowsNull());
    }
}
