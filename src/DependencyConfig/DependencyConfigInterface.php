<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\DependencyConfig;

interface DependencyConfigInterface
{
    /**
     * @psalm-return array<string, string>
     */
    public function getFactories(): array;

    public function isInvokable(string $serviceName): bool;

    public function getRealName(string $serviceName): string;

    public function getFactory(string $serviceName): ?string;

    public function hasAutowireFactory(string $serviceName): bool;

    public function hasFactory(string $serviceName): bool;
}
