<?php

declare(strict_types=1);

/**
 * @see       https://github.com/laminas/laminas-servicemanager for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\PsalmPlugin;

use Laminas\PsalmPlugin\Traverser\AliasResolver;
use Laminas\PsalmPlugin\Exception\MissingFactoryIssue;

use function class_exists;
use function in_array;
use function is_string;

final class DependencyConfig
{
    private const INVOKABLE_FACTORIES = [
        'Laminas\ServiceManager\Factory\InvokableFactory',
        'Zend\ServiceManager\Factory\InvokableFactory'
    ];

    /**
     * @psalm-var array<string, string>
     */
    private $factories;

    /**
     * @psalm-var list<string>
     */
    private $invokables;

    /**
     * @psalm-var array<string, string>
     */
    private $resolvedAliases;


    /**
     * @psalm-var array<string, string> $dependencies
     */
    public function __construct(array $dependencies)
    {
        $this->factories = $this->getValidFactories($dependencies);
        $this->invokables = $this->getValidInvokables($dependencies);
        $this->resolvedAliases = $this->getValidResolvedAliases($dependencies);
    }

    /**
     * @return array
     */
    public function getFactories(): array
    {
        return $this->factories;
    }

    /**
     * @param string $serviceName
     * @return string
     */
    public function getRealName(string $serviceName): string
    {
        return $this->resolvedAliases[$serviceName] ?? $serviceName;
    }

    /**
     * @param string $serviceName
     * @return bool
     */
    public function isInvokable(string $serviceName): bool
    {
        $realServiceName = $this->getRealName($serviceName);
        $isInvokable = in_array($realServiceName, $this->invokables, true);
        $hasInvokableFactory = in_array($this->getFactory($realServiceName), self::INVOKABLE_FACTORIES);

        return $isInvokable || $hasInvokableFactory;
    }

    /**
     * @param string $serviceName
     * @return bool
     */
    public function hasFactory(string $serviceName): bool
    {
        // TODO check if invokable/FactoryInterface

        return $this->getFactory($serviceName) !== null;
    }

    /**
     * @param string $serviceName
     * @return string|null
     */
    public function getFactory(string $serviceName): ?string
    {
        $realName = $this->getRealName($serviceName);

        return $this->factories[$realName] ?? null;
    }

    /**
     * @psalm-var array<string, string> $dependencies
     * @psalm-return array<string, string>
     *
     * @param array $dependencies
     * @return array
     */
    private function getValidFactories(array $dependencies): array
    {
        // TODO implement more checks here
        $factories = $dependencies['factories'] ?? [];
        foreach ($factories as $serviceName => $factoryClass) {
            // I saw some cases with Service::class => null, don't think we should allow it here
            if (! is_string($factoryClass) || ! class_exists($factoryClass)) {
                throw new MissingFactoryIssue($serviceName);
            }
        }

        return $factories;
    }

    /**
     * @psalm-var array<string, string> $dependencies
     * @psalm-return list<string>
     *
     * @param array $dependencies
     * @return array
     */
    private function getValidInvokables(array $dependencies): array
    {
        // TODO implement more checks here

        return $dependencies['invokables'] ?? [];
    }

    /**
     * @psalm-var array<string, string> $dependencies
     * @psalm-return array<string, string>
     *
     * @param array $dependencies
     * @return array
     */
    private function getValidResolvedAliases(array $dependencies): array
    {
        // TODO implement more checks here

        return (new AliasResolver())($dependencies['aliases'] ?? []);
    }
}
