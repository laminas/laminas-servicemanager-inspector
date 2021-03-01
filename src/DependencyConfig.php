<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector;

use Laminas\ServiceManager\Inspector\Exception\MissingFactoryException;
use Laminas\ServiceManager\Inspector\Traverser\AliasResolver;
use Zakirullin\Mess\Mess;

use function class_exists;
use function in_array;
use function is_string;

final class DependencyConfig
{
    private const INVOKABLE_FACTORIES = [
        'Laminas\ServiceManager\Factory\InvokableFactory',
        'Zend\ServiceManager\Factory\InvokableFactory',
    ];

    private const PREDEFINED_CONTAINER_KEYS = [
        'config',
    ];

    /** @psalm-var array<string, string> */
    private $factories;

    /** @psalm-var list<string> */
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
        $factories = (new Mess($dependencies))['factories']->findArrayOfStringToString() ?? [];
        foreach ($factories as $serviceName => $factoryClass) {
            if (in_array($serviceName, self::PREDEFINED_CONTAINER_KEYS, true)) {
                continue;
            }

            if (! is_string($factoryClass) || ! class_exists($factoryClass)) {
                throw new MissingFactoryException($serviceName);
            }
        }

        return $factories;
    }

    /**
     * @psalm-var array<string, string> $dependencies
     * @psalm-return list<string>|array<string, string>
     * @param array $dependencies
     * @return array
     */
    private function getValidInvokables(array $dependencies): array
    {
        $messedInvokables = (new Mess($dependencies))['invokables'];

        // FIXME findArrayOfStringToString
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
        $aliases = (new Mess($dependencies))['aliases']->findArrayOfStringToString() ?? [];

        return (new AliasResolver())($aliases);
    }

    /**
     * @return array
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
        $hasInvokableFactory = in_array($this->getFactory($realServiceName), self::INVOKABLE_FACTORIES);

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

    public function hasFactory(string $serviceName): bool
    {
        // TODO check if invokable/FactoryInterface
        if (in_array($serviceName, self::PREDEFINED_CONTAINER_KEYS, true)) {
            return true;
        }

        return $this->getFactory($serviceName) !== null;
    }
}
