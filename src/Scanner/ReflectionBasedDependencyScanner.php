<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\Scanner;

use Laminas\ServiceManager\Inspector\Dependency\Dependency;
use Laminas\ServiceManager\Inspector\DependencyConfig\DependencyConfigInterface;
use Laminas\ServiceManager\Inspector\Event\UnexpectedScalarDetectedEvent;
use Laminas\ServiceManager\Inspector\EventCollector\EventCollectorInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionParameter;

use function in_array;

final class ReflectionBasedDependencyScanner implements DependencyScannerInterface
{
    private const SUPPORTED_FACTORIES = [
        // phpcs:ignore
        'Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory',
        // phpcs:ignore
        'Zend\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory',
    ];

    /** @var DependencyConfigInterface */
    private $config;

    /** @var EventCollectorInterface */
    private $eventCollector;

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
            $className = $this->getParameterClassName($parameter);
            if ($className === null && ! $this->isOptional($parameter)) {
                $this->eventCollector->collect(new UnexpectedScalarDetectedEvent($serviceName, $parameter->getName()));
                return [];
            }

            /** @psalm-var string $className */
            $realDependencyName = $this->config->getRealName($className);

            $unsatisfiedDependencies[] = new Dependency($realDependencyName, $this->isOptional($parameter));
        }

        return $unsatisfiedDependencies;
    }

    private function isOptional(ReflectionParameter $parameter): bool
    {
        return $parameter->isOptional() || ($parameter->hasType() && $parameter->getType()->allowsNull());
    }

    private function getParameterClassName(ReflectionParameter $parameter): ?string
    {
        $type = $parameter->getType();
        if ($type === null) {
            return null;
        }

        if (! $type instanceof ReflectionNamedType) {
            // TODO ReflectionUnionType is possible here (in PHP 8)
            return null;
        }

        if ($type->isBuiltin()) {
            return null;
        }

        return $type->getName();
    }
}
