<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\Analyzer;

use Laminas\ServiceManager\Inspector\DependencyConfig;
use Laminas\ServiceManager\Inspector\Exception\UnexpectedScalarTypeIssue;
use Laminas\ServiceManager\Inspector\Traverser\Dependency;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;

use function in_array;

final class ReflectionBasedFactoryAnalyzer implements FactoryAnalyzerInterface
{
    private const SUPPORTED_FACTORIES = [
        'Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory',
        'Zend\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory',
    ];

    /** @var DependencyConfig */
    private $config;

    public function __construct(DependencyConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @return array
     * @throws ReflectionException
     */
    public function detect(string $serviceName): array
    {
        if (! $this->canDetect($serviceName)) {
            return [];
        }

        // TODO throw an exception on interface

        $realServiceName = $this->config->getRealName($serviceName);
        // TODO Check if invokable has zero params
        if ($this->config->isInvokable($realServiceName)) {
            return [];
        }

        return $this->getConstructorParameters($serviceName);
    }

    public function canDetect(string $serviceName): bool
    {
        $class = $this->config->getFactory($serviceName);

        return in_array($class, self::SUPPORTED_FACTORIES, true);
    }

    /**
     * @return array
     * @throws ReflectionException
     */
    private function getConstructorParameters(string $serviceName): array
    {
        $reflectionClass = new ReflectionClass($serviceName);
        $constructor     = $reflectionClass->getConstructor();
        if ($constructor === null) {
            return [];
        }

        $unsatisfiedDependencies = [];
        foreach ($constructor->getParameters() as $parameter) {
            $this->assertHasClassTypeHint($parameter, $serviceName);
            $realDependencyName = $this->config->getRealName($parameter->getClass()->getName());
            if ($this->config->isInvokable($realDependencyName)) {
                continue;
            }

            $unsatisfiedDependencies[] = new Dependency($realDependencyName, $this->isOptional($parameter));
        }

        return $unsatisfiedDependencies;
    }

    private function assertHasClassTypeHint(ReflectionParameter $parameter, string $serviceName): void
    {
        if ($parameter->getClass() === null) {
            // FIXME config param
            if (! $this->isOptional($parameter)) {
                throw new UnexpectedScalarTypeIssue($serviceName, $parameter->getName());
            }
        }
    }

    private function isOptional(ReflectionParameter $parameter): bool
    {
        return $parameter->isOptional() || ($parameter->hasType() && $parameter->getType()->allowsNull());
    }
}
