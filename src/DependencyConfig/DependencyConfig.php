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

    /** @psalm-var array<string, mixed> */
    private $dependencies;

    /**
     * @psalm-param array<string, mixed> $dependencies
     * @param string[] $dependencies
     */
    public function __construct(array $dependencies)
    {
        $this->dependencies = $dependencies;
    }

    /**
     * @psalm-return array<string, string>
     * @return string[]
     */
    public function getFactories(): array
    {
        $invokableFactories = [];
        $invokables         = $this->getInvokables();
        foreach ($invokables as $name => $class) {
            if ($name !== $class) {
                $invokableFactories[$class] = InvokableFactory::class;
            }
        }

        $factories = (new Mess($this->dependencies))['factories']->findArrayOfStringToString() ?? [];

        return array_merge($invokableFactories, $factories);
    }

    /**
     * @psalm-return array<string, string>
     * @return string[]
     */
    private function getInvokables(): array
    {
        $messedInvokables = (new Mess($this->dependencies))['invokables'];

        return $messedInvokables->findArrayOfStringToString() ?? [];
    }

    /**
     * @psalm-return array<string, string>
     * @return string[]
     */
    private function getResolvedAliases(): array
    {
        $invokableAliases = [];
        $invokables       = $this->getInvokables();
        foreach ($invokables as $name => $class) {
            if ($name !== $class) {
                $invokableAliases[$name] = $class;
            }
        }

        $aliases         = (new Mess($this->dependencies))['aliases']->findArrayOfStringToString() ?? [];
        $resolvedAliases = (new AliasResolver())($aliases);

        return array_merge($invokableAliases, $resolvedAliases);
    }


    /**
     * TODO it's not a list
     */
    public function isInvokable(string $serviceName): bool
    {
        $realServiceName     = $this->getRealName($serviceName);
        $isInvokable         = in_array($realServiceName, $this->getInvokables(), true);
        $hasInvokableFactory = in_array($this->getFactory($realServiceName), self::INVOKABLE_FACTORIES, true);

        return $isInvokable || $hasInvokableFactory;
    }

    public function getRealName(string $serviceName): string
    {
        // TODO Alias resolver
        return $this->getResolvedAliases()[$serviceName] ?? $serviceName;
    }

    public function getFactory(string $serviceName): ?string
    {
        $realName = $this->getRealName($serviceName);

        return $this->getFactories()[$realName] ?? null;
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
