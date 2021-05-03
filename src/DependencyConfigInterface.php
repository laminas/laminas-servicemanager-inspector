<?php

namespace Laminas\ServiceManager\Inspector;

interface DependencyConfigInterface
{
    /**
     * @return array
     */
    public function getFactories(): array;

    /**
     * TODO it's not a list
     */
    public function isInvokable(string $serviceName): bool;

    public function getRealName(string $serviceName): string;

    public function getFactory(string $serviceName): ?string;

    public function hasAutowireFactory(string $serviceName): bool;

    public function hasFactory(string $serviceName): bool;
}