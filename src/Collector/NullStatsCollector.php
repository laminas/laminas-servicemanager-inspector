<?php

declare(strict_types=1);

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ServiceManagerInspector\Collector;

final class NullStatsCollector implements StatsCollectorInterface
{
    /**
     * @param string $dependencyName
     * @param array $instantiationStack
     */
    public function collectAutowireFactoryHit(string $dependencyName, array $instantiationStack): void
    {
    }

    /**
     * @param string $dependencyName
     * @param array $instantiationStack
     */
    public function collectCustomFactoryHit(string $dependencyName, array $instantiationStack): void
    {
    }

    /**
     * @param string $dependencyName
     * @param array $instantiationStack
     */
    public function collectError(string $dependencyName, array $instantiationStack): void
    {
    }

    public function finish(): void
    {
    }
}
