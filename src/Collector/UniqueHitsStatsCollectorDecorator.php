<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ServiceManagerInspector\Collector;

final class UniqueHitsStatsCollectorDecorator implements StatsCollectorInterface
{
    /** @var StatsCollectorInterface */
    private $collector;

    /** @psalm-var list<string> */
    private $hits = [];

    public function __construct(StatsCollectorInterface $collector)
    {
        $this->collector = $collector;
    }

    /**
     * @param array $instantiationStack
     */
    public function collectAutowireFactoryHit(string $dependencyName, array $instantiationStack): void
    {
        if (isset($this->hits[$dependencyName])) {
            return;
        }

        $this->hits[$dependencyName] = true;

        $this->collector->collectAutowireFactoryHit($dependencyName, $instantiationStack);
    }

    /**
     * @param array $instantiationStack
     */
    public function collectCustomFactoryHit(string $dependencyName, array $instantiationStack): void
    {
        if (isset($this->hits[$dependencyName])) {
            return;
        }

        $this->hits[$dependencyName] = true;

        $this->collector->collectCustomFactoryHit($dependencyName, $instantiationStack);
    }

    /**
     * @param array $instantiationStack
     */
    public function collectError(string $dependencyName, array $instantiationStack): void
    {
        $this->collector->collectError($dependencyName, $instantiationStack);
    }

    public function finish(): void
    {
        $this->collector->finish();
    }
}
