<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\EventCollector;

final class NullListener implements ListenerInterface
{
    public function enterInvokable(string $dependencyName, array $instantiationStack): void
    {
    }

    public function enterAutowireFactory(string $dependencyName, array $instantiationStack): void
    {
    }

    public function enterCustomFactory(string $dependencyName, array $instantiationStack): void
    {
    }

    public function enterError(string $dependencyName, array $instantiationStack): void
    {
    }

    public function render(): void
    {
    }
}
