<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector;

use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\ServiceManager\Inspector\Exception\MissingFactoryException;
use Zakirullin\Mess\Mess;

use function array_merge;
use function class_exists;
use function in_array;
use function is_string;

final class DependencyConfig implements DependencyConfigInterface
{
    private const INVOKABLE_FACTORIES = [
        // phpcs:ignore
        'Laminas\ServiceManager\Factory\InvokableFactory',
        // phpcs:ignore
        'Zend\ServiceManager\Factory\InvokableFactory',
    ];

    private const AUTOWIRE_FACTORIES = [
        // phpcs:ignore
        'Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory',
        // phpcs:ignore
        'Zend\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory',
    ];

    private const PREDEFINED_CONTAINER_KEYS = [
        'config',
    ];

    /** @psalm-var array<string, string> */
    private $factories;

    /** @psalm-var array<string, string> */
    private $invokables;

    /** @psalm-var array<string, string> */
    private $resolvedAliases;

    /**
     * @psalm-var array<string, string> $dependencies
     */
    public function __construct(array $dependencies)
    {
        $this->factories       = $this->getValidFactories($dependencies);
        $this->invokables      = $this->getValidInvokables($dependencies);
        $this->resolvedAliases = $this->getValidResolvedAliases($dependencies);
    }

    /**
     * @psalm-var array<string, string> $dependencies
     * @psalm-return array<string, string>
     * @param array $dependencies
     * @return array
     */
    private function getValidFactories(array $dependencies): array
    {
        $invokableFactories = [];
        $invokables         = (new Mess($dependencies))['invokables']->findArray() ?? [];
        foreach ($invokables as $name => $class) {
            if ($name !== $class) {
                $invokableFactories[$class] = InvokableFactory::class;
            }
        }

        $factories = (new Mess($dependencies))['factories']->findArray() ?? [];
        foreach ($factories as $serviceName => $factoryClass) {
            if (in_array($serviceName, self::PREDEFINED_CONTAINER_KEYS, true)) {
                continue;
            }

            // TODO an exception?
            if (! is_string($factoryClass) || ! class_exists($factoryClass)) {
                throw new MissingFactoryException($serviceName);
            }
        }

        return array_merge($invokableFactories, $factories);
    }

    /**
     * @psalm-var array<string, string> $dependencies
     * @psalm-return array<string, string>
     * @param array $dependencies
     * @return array
     */
    private function getValidInvokables(array $dependencies): array
    {
        $messedInvokables = (new Mess($dependencies))['invokables'];

        // FIXME string
        return $messedInvokables->findArray() ?? [];
    }

    /**
     * @psalm-var array<string, string> $dependencies
     * @psalm-return array<string, string>
     * @param array $dependencies
     * @return array
     */
    private function getValidResolvedAliases(array $dependencies): array
    {
        $invokableAliases = [];
        $invokables       = (new Mess($dependencies))['invokables']->findArray() ?? [];
        foreach ($invokables as $name => $class) {
            if ($name !== $class) {
                $invokableAliases[$name] = $class;
            }
        }

        $aliases = (new Mess($dependencies))['aliases']->findArray() ?? [];

        return array_merge($invokableAliases, $aliases);
    }

    /**
     * @psalm-var array<string, string>
     */
    public function getFactories(): array
    {
        return $this->factories;
    }

    /**
     * TODO it's not a list
     */
    public function isInvokable(string $serviceName): bool
    {
        $realServiceName     = $this->getRealName($serviceName);
        $isInvokable         = in_array($realServiceName, $this->invokables, true);
        $hasInvokableFactory = in_array($this->getFactory($realServiceName), self::INVOKABLE_FACTORIES, true);

        return $isInvokable || $hasInvokableFactory;
    }

    public function getRealName(string $serviceName): string
    {
        return $this->resolvedAliases[$serviceName] ?? $serviceName;
    }

    public function getFactory(string $serviceName): ?string
    {
        $realName = $this->getRealName($serviceName);

        return $this->factories[$realName] ?? null;
    }

    public function hasAutowireFactory(string $serviceName): bool
    {
        return in_array($this->getFactory($serviceName), self::AUTOWIRE_FACTORIES);
    }

    public function hasFactory(string $serviceName): bool
    {
        // TODO check if invokable/FactoryInterface
        if (in_array($serviceName, self::PREDEFINED_CONTAINER_KEYS, true)) {
            return true;
        }

        return $this->getFactory($serviceName) !== null;
    }
}
