<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\DependencyConfig;

use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\ServiceManager\Inspector\AliasResolver\AliasResolver;
use Zakirullin\Mess\Mess;

use function array_merge;
use function class_exists;
use function in_array;

/**
 * Provides a convenient abstraction over raw ServiceManager configuration.
 */
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
     * @psalm-param array<string, mixed> $dependencies
     * @param string[] $dependencies
     */
    public function __construct(array $dependencies)
    {
        $this->factories       = $this->getValidFactories($dependencies);
        $this->invokables      = $this->getValidInvokables($dependencies);
        $this->resolvedAliases = $this->getValidResolvedAliases($dependencies);
    }

    /**
     * @psalm-param array<string, mixed> $dependencies
     * @param string[] $dependencies
     * @psalm-return array<string, string>
     * @return string[]
     */
    private function getValidFactories(array $dependencies): array
    {
        $invokableFactories = [];
        $invokables         = $this->getValidInvokables($dependencies);
        foreach ($invokables as $name => $class) {
            if ($name !== $class) {
                $invokableFactories[$class] = InvokableFactory::class;
            }
        }

        $factories = (new Mess($dependencies))['factories']->findArrayOfStringToString() ?? [];

        return array_merge($invokableFactories, $factories);
    }

    /**
     * @psalm-param array<string, mixed> $dependencies
     * @param string[] $dependencies
     * @psalm-return array<string, string>
     * @return string[]
     */
    private function getValidInvokables(array $dependencies): array
    {
        $messedInvokables = (new Mess($dependencies))['invokables'];

        return $messedInvokables->findArray() ?? [];
    }

    /**
     * @psalm-param array<string, mixed> $dependencies
     * @param string[] $dependencies
     * @psalm-return array<string, string>
     * @return string[]
     */
    private function getValidResolvedAliases(array $dependencies): array
    {
        $invokableAliases = [];
        $invokables       = $this->getValidInvokables($dependencies);
        foreach ($invokables as $name => $class) {
            if ($name !== $class) {
                $invokableAliases[$name] = $class;
            }
        }

        $aliases         = (new Mess($dependencies))['aliases']->findArrayOfStringToString() ?? [];
        $resolvedAliases = (new AliasResolver())($aliases);

        return array_merge($invokableAliases, $resolvedAliases);
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
        // TODO Alias resolver
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

        $class = $this->getFactory($serviceName);
        if ($class === null) {
            return false;
        }

        return class_exists($class);
    }
}
